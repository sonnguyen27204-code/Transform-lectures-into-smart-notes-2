<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Throwable;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;
    protected int $timeout;
    protected int $maxRetries;
    protected int $retryDelay;
    /** @var string[] */
    protected array $fallbackModels;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->model = config('gemini.model', 'gemini-2.0-flash');
        $this->endpoint = config('gemini.endpoint');
        $this->timeout = (int) config('gemini.timeout', 180);
        $this->maxRetries = 3;
        $this->retryDelay = 3;

        // Fallback chain: thu model chinh truoc, neu loi se tu dong chuyen
        $fbRaw = config('gemini.fallback_models', []);
        if (is_string($fbRaw)) {
            $fbRaw = array_values(array_filter(array_map('trim', explode(',', $fbRaw))));
        }
        if (!is_array($fbRaw)) {
            $fbRaw = [];
        }
        $this->fallbackModels = $fbRaw;

        // Luon dat model chinh o dau tien neu chua co
        if (!empty($this->model) && !in_array($this->model, $this->fallbackModels, true)) {
            array_unshift($this->fallbackModels, $this->model);
        }

        // Dam bao co it nhat 1 model
        if (empty($this->fallbackModels)) {
            $this->fallbackModels = ['gemini-2.0-flash'];
        }
    }

    /**
     * Xử lý audio: transcript + summary + quiz + flashcard.
     * Co retry + tu dong fallback sang model khac khi gap loi 404/429.
     */
    public function processAudio(string $audioPath, string $mimeType): array
    {
        $this->validateApiKey();
        $this->validateFileExists($audioPath);

        $audioBase64 = base64_encode(file_get_contents($audioPath));
        $prompt = $this->buildPrompt();
        $payload = $this->buildPayload($prompt, $audioBase64, $mimeType);

        $lastException = null;
        $triedModels = [];

        foreach ($this->fallbackModels as $model) {
            $url = $this->endpoint . $model . ':generateContent?key=' . $this->apiKey;
            $triedModels[] = $model;

            Log::info('Gemini: thu model', ['model' => $model]);

            for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
                try {
                    $response = Http::timeout($this->timeout)
                        ->connectTimeout(15)
                        ->retry(0, 0)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                            'User-Agent' => 'Aurora-AI/1.0',
                        ])
                        ->withOptions([
                            'http_errors' => false,
                            'force_ip_resolve' => 'v4',
                        ])
                        ->post($url, $payload);

                    $statusCode = $response->status();

                    if ($response->successful()) {
                        $result = $this->parseResponse($response->json());
                        Log::info('Gemini API success', [
                            'file' => basename($audioPath),
                            'model' => $model,
                            'attempt' => $attempt,
                        ]);
                        return $result;
                    }

                    $errorBody = $response->body();
                    Log::warning('Gemini API error', [
                        'status' => $statusCode,
                        'model' => $model,
                        'attempt' => $attempt . '/' . $this->maxRetries,
                        'body_preview' => substr($errorBody, 0, 200),
                    ]);

                    // 404 model khong ton tai / bi flag -> chuyen model tiep theo ngay
                    if ($statusCode === 404) {
                        Log::warning('Gemini model bi 404, chuyen model tiep theo', ['model' => $model]);
                        break; // thoat vong lap attempt, chuyen model khac
                    }

                    // 429 quota het -> retry backoff, neu het attempt thi chuyen model
                    if ($statusCode === 429) {
                        if ($attempt < $this->maxRetries) {
                            sleep($this->retryDelay * $attempt);
                            continue;
                        }
                        Log::warning('Gemini model het quota 429, chuyen model tiep theo', ['model' => $model]);
                        break;
                    }

                    // 400 (bad request - co the prompt khong tuong thich) -> chuyen model
                    if ($statusCode === 400) {
                        Log::warning('Gemini 400 Bad Request, chuyen model tiep theo', ['model' => $model]);
                        break;
                    }

                    // 401/403 -> key hong -> break luon, fallback khong cuu
                    if ($statusCode === 401 || $statusCode === 403) {
                        throw new RuntimeException(
                            "Gemini API key khong hop le hoac bi chan (HTTP {$statusCode}). " .
                            "Vui long lay key moi tai: https://aistudio.google.com/app/apikey"
                        );
                    }

                    // 5xx -> retry
                    if ($attempt < $this->maxRetries) {
                        sleep($this->retryDelay * $attempt);
                    }
                } catch (RuntimeException $e) {
                    throw $e;
                } catch (Throwable $e) {
                    $lastException = $e;
                    $isNetworkError = $this->isNetworkError($e);
                    Log::warning('Gemini network error', [
                        'model' => $model,
                        'attempt' => $attempt,
                        'message' => $e->getMessage(),
                        'network_error' => $isNetworkError,
                    ]);
                    if ($attempt < $this->maxRetries && $isNetworkError) {
                        sleep($this->retryDelay * $attempt);
                        continue;
                    }
                    // loi mang nhung khong phai retry -> thoat vong attempt
                    break;
                }
            }
        }

        // Het fallback models van khong duoc
        $tried = implode(', ', $triedModels);
        throw new RuntimeException(
            "Gemini API that bai voi tat ca models ({$tried}). " .
            "Vui long kiem tra API key hoac quota. " .
            "Chi tiet: " . ($lastException ? $lastException->getMessage() : 'Khong ro nguyen nhan')
        );
    }

    /**
     * Kiem tra API key co hop le khong (cache ket qua 5 phut).
     * Test qua nhieu models de phat hien model nao con hoat dong.
     */
    public function validateApiKey(): void
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'GEMINI_API_KEY chua duoc cau hinh. ' .
                'Vui long them vao file .env (dong GEMINI_API_KEY=...)'
            );
        }

        $cacheKey = 'gemini_api_key_valid_' . md5($this->apiKey);

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);
            if ($cached === false) {
                throw new RuntimeException('GEMINI_API_KEY khong hop le hoac da het han.');
            }
            return;
        }

        // Test nhieu models de xem co it nhat 1 con hoat dong
        $modelsToTest = $this->fallbackModels ?: [$this->model];
        $anyWorking = false;
        $lastStatus = 0;
        $lastMsg = '';

        foreach ($modelsToTest as $model) {
            try {
                $testUrl = $this->endpoint . $model . ':generateContent?key=' . $this->apiKey;
                $response = Http::timeout(8)->connectTimeout(5)->post($testUrl, [
                    'contents' => [['parts' => [['text' => 'Hi']]]],
                    'generationConfig' => ['maxOutputTokens' => 5],
                ]);

                $status = $response->status();
                $lastStatus = $status;
                $lastMsg = substr($response->body(), 0, 150);

                if ($response->successful()) {
                    $anyWorking = true;
                    Log::info('Gemini key validation OK voi model', ['model' => $model]);
                    break;
                }

                // 403/401: key that bai hoan toan
                if ($status === 403 || $status === 401) {
                    Cache::put($cacheKey, false, 300);
                    throw new RuntimeException(
                        "Gemini API key bi Google tu choi (HTTP {$status}). " .
                        "Vui long lay key moi tai: https://aistudio.google.com/app/apikey"
                    );
                }

                // 429 (quota 0) hoac 404 (banned) -> tiep tuc thu model khac
            } catch (RuntimeException $e) {
                throw $e;
            } catch (Throwable $e) {
                // Network error - bo qua, thu model tiep theo
                Log::warning('Gemini validation network error', ['model' => $model, 'message' => $e->getMessage()]);
            }
        }

        if (!$anyWorking && ($lastStatus === 429 || $lastStatus === 404)) {
            // Key hop le nhung tat ca models bi 429/404 (thuong do account bi Google flag)
            // Van cho phep su dung - service se tu fallback
            Log::warning('Tat ca Gemini models bi 429/404, service se tu fallback', [
                'last_status' => $lastStatus,
                'last_msg' => $lastMsg,
            ]);
            Cache::put($cacheKey, true, 60); // cache ngan de retry sau
            return;
        }

        if (!$anyWorking) {
            // Loi khac - assume network issue, allow retry
            Cache::put($cacheKey, true, 60);
            return;
        }

        Cache::put($cacheKey, true, 300);
    }

    protected function validateFileExists(string $audioPath): void
    {
        if (!file_exists($audioPath)) {
            throw new RuntimeException("File audio không tồn tại: {$audioPath}");
        }

        $fileSize = filesize($audioPath);
        if ($fileSize === 0) {
            throw new RuntimeException("File audio rỗng: {$audioPath}");
        }

        // Max 50MB
        if ($fileSize > 50 * 1024 * 1024) {
            throw new RuntimeException("File audio quá lớn (tối đa 50MB). Kích thước hiện tại: " . round($fileSize / 1024 / 1024, 1) . "MB");
        }
    }

    protected function buildPrompt(): string
    {
        return <<<'PROMPT'
Bạn là trợ lý AI chuyên xử lý bài giảng tiếng Việt. Hãy phân tích file âm thanh được cung cấp và trả về DUY NHẤT một JSON hợp lệ (không có markdown, không có ```json, không giải thích gì thêm) theo cấu trúc sau:

{
  "language": "vi",
  "transcript": {
    "full_text": "Toàn bộ văn bản chuyển đổi, giữ nguyên tiếng Việt có dấu",
    "word_count": 123,
    "segments": [
      {
        "start": 0.0,
        "end": 12.5,
        "text": "Đoạn văn nói",
        "speaker": "teacher",
        "speaker_label": "Giảng viên"
      }
    ]
  },
  "summary": {
    "brief": "Tóm tắt ngắn gọn 2-4 câu về nội dung chính",
    "key_takeaways": [
      "Ý chính 1",
      "Ý chính 2",
      "Ý chính 3",
      "Ý chính 4",
      "Ý chính 5"
    ],
    "topics": ["Chủ đề 1", "Chủ đề 2"],
    "sentiment": "neutral"
  },
  "quizzes": [
    {
      "question": "Câu hỏi trắc nghiệm?",
      "options": [
        "Đáp án A",
        "Đáp án B",
        "Đáp án C",
        "Đáp án D"
      ],
      "correct_index": 0,
      "explanation": "Giải thích ngắn",
      "difficulty": "easy",
      "topic": "Chủ đề"
    }
  ],
  "flashcards": [
    {
      "term": "Thuật ngữ",
      "definition": "Định nghĩa",
      "example": "Ví dụ minh họa (nếu có)"
    }
  ]
}

YÊU CẦU:
1. Transcript phải chính xác, đầy đủ, giữ nguyên tiếng Việt có dấu.
2. Mỗi segment khoảng 5-15 giây, có speaker (teacher/student/unknown).
3. Tạo 5-8 key_takeaways, mỗi cái 1 dòng ngắn gọn.
4. Tạo 5-8 câu quiz, đa dạng độ khó (easy/medium/hard), mỗi câu 4 đáp án, chỉ rõ correct_index (0-3).
5. Tạo 5-10 flashcard thuật ngữ quan trọng.
6. CHỈ trả về JSON, KHÔNG có text nào khác.
PROMPT;
    }

    protected function buildPayload(string $prompt, string $audioBase64, string $mimeType): array
    {
        return [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    [
                        'inline_data' => [
                            'mime_type' => $mimeType,
                            'data' => $audioBase64,
                        ],
                    ],
                ],
            ]],
            'generationConfig' => [
                'temperature' => 0.4,
                'topK' => 32,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
                'response_mime_type' => 'application/json',
            ],
        ];
    }

    protected function parseResponse(array $data): array
    {
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            throw new RuntimeException('Gemini trả về phản hồi rỗng.');
        }

        // Loại bỏ markdown code block nếu có
        $text = trim($text);
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/^```\s*/i', '', $text);
        $text = preg_replace('/\s*```$/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Gemini JSON decode error', ['raw' => $text, 'error' => json_last_error_msg()]);
            throw new RuntimeException('Phản hồi từ AI không phải JSON hợp lệ: ' . json_last_error_msg());
        }

        return $this->normalizeResult($decoded);
    }

    /**
     * Chuẩn hóa kết quả để đảm bảo có đủ các field.
     */
    protected function normalizeResult(array $data): array
    {
        return [
            'language' => $data['language'] ?? 'vi',
            'transcript' => [
                'full_text' => $data['transcript']['full_text'] ?? '',
                'word_count' => (int) ($data['transcript']['word_count'] ?? str_word_count($data['transcript']['full_text'] ?? '')),
                'segments' => array_map(function ($seg) {
                    return [
                        'start' => (float) ($seg['start'] ?? 0),
                        'end' => (float) ($seg['end'] ?? 0),
                        'text' => $seg['text'] ?? '',
                        'speaker' => in_array($seg['speaker'] ?? '', ['teacher', 'student']) ? $seg['speaker'] : 'unknown',
                        'speaker_label' => $seg['speaker_label'] ?? null,
                    ];
                }, $data['transcript']['segments'] ?? []),
            ],
            'summary' => [
                'brief' => $data['summary']['brief'] ?? '',
                'key_takeaways' => $data['summary']['key_takeaways'] ?? [],
                'topics' => $data['summary']['topics'] ?? [],
                'sentiment' => $data['summary']['sentiment'] ?? 'neutral',
            ],
            'quizzes' => array_map(function ($quiz) {
                $correctIndex = isset($quiz['correct_index']) ? (int) $quiz['correct_index'] : 0;
                $correctIndex = max(0, min(3, $correctIndex));
                return [
                    'question' => $quiz['question'] ?? '',
                    'options' => array_slice($quiz['options'] ?? [], 0, 4),
                    'correct_index' => $correctIndex,
                    'explanation' => $quiz['explanation'] ?? null,
                    'difficulty' => in_array($quiz['difficulty'] ?? '', ['easy', 'medium', 'hard']) ? $quiz['difficulty'] : 'medium',
                    'topic' => $quiz['topic'] ?? null,
                ];
            }, $data['quizzes'] ?? []),
            'flashcards' => array_map(function ($card) {
                return [
                    'term' => $card['term'] ?? '',
                    'definition' => $card['definition'] ?? '',
                    'example' => $card['example'] ?? null,
                ];
            }, $data['flashcards'] ?? []),
        ];
    }

    protected function isNetworkError(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, 'curl error')
            || str_contains($message, 'connection refused')
            || str_contains($message, 'timeout')
            || str_contains($message, 'connection reset')
            || str_contains($message, 'network')
            || str_contains($message, 'socket');
    }

    protected function throwDetailedException(int $statusCode, string $body): void
    {
        $bodyArray = json_decode($body, true);
        $errorMessage = $bodyArray['error']['message'] ?? $body;

        $knownErrors = [
            400 => 'Yêu cầu không hợp lệ. Vui lòng kiểm tra định dạng file audio.',
            401 => 'API Key không hợp lệ. Vui lòng kiểm tra GEMINI_API_KEY trong file .env',
            403 => 'Không có quyền truy cập. API Key có thể đã bị vô hiệu hóa.',
            404 => 'Model không tồn tại. Vui lòng kiểm tra GEMINI_MODEL trong file .env',
            429 => 'Đã vượt quá giới hạn yêu cầu. Vui lòng đợi và thử lại sau.',
            500 => 'Lỗi máy chủ Google. Vui lòng thử lại sau.',
            503 => 'Dịch vụ tạm thời không khả dụng. Vui lòng thử lại sau.',
        ];

        $friendlyMessage = $knownErrors[$statusCode] ?? "Lỗi không xác định (HTTP {$statusCode})";
        
        throw new RuntimeException("{$friendlyMessage}\n\nChi tiết: " . substr($errorMessage, 0, 200));
    }

    protected function logApiError(int $statusCode, string $message, int $attempt, bool $willRetry): void
    {
        // Deprecated: logs are now inline in processAudio()
        Log::warning('Gemini API error (legacy)', [
            'status' => $statusCode,
            'message' => substr($message, 0, 300),
            'attempt' => $attempt . '/' . $this->maxRetries,
            'will_retry' => $willRetry,
            'model' => $this->model,
        ]);
    }
}
