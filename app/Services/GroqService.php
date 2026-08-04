<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Groq API - backup provider khi Gemini fail.
 * Dang ky mien phi tai: https://console.groq.com/keys
 *
 * Luu y: Groq chi ho tro text (khong nhan audio truc tiep).
 * Can ket hop voi OpenAI Whisper hoac Gemini de lay transcript truoc,
 * roi dung Groq de tao summary/quiz/flashcard.
 */
class GroqService
{
    protected string $apiKey;
    protected string $model;
    protected string $endpoint;
    protected int $timeout;

    public function __construct()
    {
        $this->apiKey = (string) config('gemini.groq.api_key');
        $this->model = (string) config('gemini.groq.model', 'llama-3.3-70b-versatile');
        $this->endpoint = (string) config('gemini.groq.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $this->timeout = 120;
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Sinh summary/quiz/flashcard tu transcript (van ban da co san).
     * Tra ve array cung format voi GeminiService::normalizeResult().
     */
    public function generateFromTranscript(array $transcriptData): array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('GROQ_API_KEY chua duoc cau hinh trong file .env');
        }

        $fullText = $transcriptData['transcript']['full_text'] ?? '';
        if (empty($fullText)) {
            throw new RuntimeException('Transcript rong - khong the tao summary.');
        }

        $prompt = $this->buildPrompt($fullText);

        $payload = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Ban la tro ly AI chuyen xu ly bai giang tieng Viet. Luon tra ve JSON hop le, khong markdown, khong giai thich.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
            'temperature' => 0.4,
            'max_tokens' => 8192,
            'response_format' => ['type' => 'json_object'],
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->connectTimeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->withOptions(['http_errors' => false])
                ->post($this->endpoint, $payload);

            if (!$response->successful()) {
                $body = $response->body();
                Log::error('Groq API error', ['status' => $response->status(), 'body' => substr($body, 0, 300)]);
                throw new RuntimeException("Groq API loi (HTTP {$response->status()}): " . substr($body, 0, 200));
            }

            $data = $response->json();
            $text = $data['choices'][0]['message']['content'] ?? null;
            if (!$text) {
                throw new RuntimeException('Groq tra ve phan hoi rong.');
            }

            $decoded = json_decode($text, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('Groq response khong phai JSON: ' . json_last_error_msg());
            }

            // Giu transcript goc, chi tao phan summary/quiz/flashcard moi
            return [
                'language' => $decoded['language'] ?? 'vi',
                'transcript' => $transcriptData['transcript'],
                'summary' => [
                    'brief' => $decoded['summary']['brief'] ?? '',
                    'key_takeaways' => $decoded['summary']['key_takeaways'] ?? [],
                    'topics' => $decoded['summary']['topics'] ?? [],
                    'sentiment' => $decoded['summary']['sentiment'] ?? 'neutral',
                ],
                'quizzes' => $decoded['quizzes'] ?? [],
                'flashcards' => $decoded['flashcards'] ?? [],
            ];
        } catch (RuntimeException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Groq exception', ['message' => $e->getMessage()]);
            throw new RuntimeException('Loi ket noi Groq: ' . $e->getMessage());
        }
    }

    protected function buildPrompt(string $fullText): string
    {
        // Gioi han transcript de tranh qua lon (Groq co gioi han token)
        $text = mb_strlen($fullText) > 30000 ? mb_substr($fullText, 0, 30000) . '...' : $fullText;

        return <<<PROMPT
Phan tich transcript bai giang tieng Viet duoi day va tra ve DUY NHAT mot JSON (khong markdown, khong ```json, khong giai thich them) theo cau truc:

{
  "language": "vi",
  "summary": {
    "brief": "Tom tat ngan gon 2-4 cau ve noi dung chinh",
    "key_takeaways": [
      "Y chinh 1",
      "Y chinh 2",
      "Y chinh 3",
      "Y chinh 4",
      "Y chinh 5"
    ],
    "topics": ["Chu de 1", "Chu de 2"],
    "sentiment": "neutral"
  },
  "quizzes": [
    {
      "question": "Cau hoi trac nghiem?",
      "options": ["Dap an A", "Dap an B", "Dap an C", "Dap an D"],
      "correct_index": 0,
      "explanation": "Giai thich ngan",
      "difficulty": "easy",
      "topic": "Chu de"
    }
  ],
  "flashcards": [
    {
      "term": "Thuat ngu",
      "definition": "Dinh nghia",
      "example": "Vi du minh hoa"
    }
  ]
}

YEU CAU:
1. Tao 5-8 key_takeaways, moi cai 1 dong ngan gon.
2. Tao 5-8 cau quiz, moi cau 4 dap an, chi ro correct_index (0-3).
3. Tao 5-10 flashcard thuat ngu quan trong.
4. CHI tra ve JSON, KHONG co text nao khac.

TRANSCRIPT:
---
{$text}
---
PROMPT;
    }
}