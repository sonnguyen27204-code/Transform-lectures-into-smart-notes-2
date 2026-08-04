<?php

namespace App\Services;

use App\Models\Lecture;
use App\Models\ProcessingJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AudioProcessingService
{
    public function __construct(
        protected GeminiService $gemini,
        protected GroqService $groq
    ) {}

    /**
     * Pipeline xử lý audio hoàn chỉnh:
     * transcribing → analyzing → generating → completed
     *
     * Multi-provider fallback:
     *  1. Gemini (multi-model fallback) - ưu tiên #1
     *  2. OpenAI Whisper (transcript) + Groq (summary/quiz) - nếu Gemini fail
     *  3. Nếu tất cả fail → báo lỗi chi tiết
     */
    public function process(Lecture $lecture): void
    {
        $lecture->refresh();

        $audioPath = $lecture->audio_path
            ? storage_path('app/public/' . $lecture->audio_path)
            : null;

        if (!$audioPath || !file_exists($audioPath)) {
            throw new RuntimeException('File audio không tồn tại trên server.');
        }

        $convertedPath = null;
        try {
            $this->updateStage($lecture, 'transcribing', 15, 'Đang chuyển giọng nói thành văn bản...');

            $mimeType = $lecture->mime_type ?? 'audio/mpeg';

            if (in_array($mimeType, ['audio/webm', 'video/webm', 'audio/mp4', 'audio/x-m4a', 'audio/m4a'])) {
                $convertedPath = $this->convertToOgg($audioPath, $mimeType);
                if ($convertedPath) {
                    $audioPath = $convertedPath;
                    $mimeType = 'audio/ogg';
                }
            }

            $result = $this->transcribeAndAnalyze($audioPath, $mimeType, $lecture);

            if ($convertedPath && file_exists($convertedPath)) {
                @unlink($convertedPath);
                $convertedPath = null;
            }

            $this->updateStage($lecture, 'analyzing', 50, 'Đang phân tích nội dung...');
            $this->saveTranscript($lecture, $result['transcript'], $result['language']);

            $this->updateStage($lecture, 'generating', 80, 'Đang tạo tóm tắt, câu hỏi và thẻ ghi nhớ...');
            $this->saveSummary($lecture, $result['summary']);
            $this->saveQuizzes($lecture, $result['quizzes']);
            $this->saveFlashcards($lecture, $result['flashcards']);

            $lecture->update([
                'status' => 'completed',
                'processed_at' => now(),
                'error_message' => null,
            ]);

            $provider = $result['_provider'] ?? 'gemini';
            $this->updateStage($lecture, 'completed', 100, "Hoàn thành! (xử lý bởi: {$provider})");
        } catch (\Throwable $e) {
            Log::error('Audio processing failed', [
                'lecture_id' => $lecture->id,
                'error' => $e->getMessage(),
            ]);

            $lecture->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $this->updateStage($lecture, 'failed', 0, 'Lỗi: ' . $e->getMessage());

            throw $e;
        } finally {
            if ($convertedPath && file_exists($convertedPath)) {
                @unlink($convertedPath);
            }
        }
    }

    /**
     * Transcribe + analyze voi multi-provider fallback.
     *
     * Thu tu:
     *  1. Gemini (multi-model fallback) - tra ve day du transcript + summary + quiz + flashcard
     *  2. Whisper (lay transcript) + Groq (tao summary/quiz/flashcard tu transcript)
     *  3. Throw loi neu ca 2 cach deu fail
     */
    protected function transcribeAndAnalyze(string $audioPath, string $mimeType, Lecture $lecture): array
    {
        $errors = [];

        // ===== CACH 1: Gemini (multi-model fallback ben trong) =====
        try {
            $this->updateStage($lecture, 'transcribing', 20, 'Đang thử xử lý bằng Gemini AI...');
            $result = $this->gemini->processAudio($audioPath, $mimeType);
            $result['_provider'] = 'gemini';
            Log::info('Processing succeed via Gemini', ['lecture_id' => $lecture->id]);
            return $result;
        } catch (\Throwable $e) {
            $errors[] = 'Gemini: ' . $e->getMessage();
            Log::warning('Gemini failed, se thu fallback', ['error' => $e->getMessage()]);
        }

        // ===== CACH 2: OpenAI Whisper + Groq =====
        $this->updateStage($lecture, 'transcribing', 35, 'Gemini không khả dụng - chuyển sang provider dự phòng...');

        $transcript = $this->transcribeWithWhisperOrFallback($audioPath, $lecture);
        if ($transcript === null) {
            $this->updateStage($lecture, 'transcribing', 35, 'Tất cả provider đều không thể lấy transcript.');
            throw new RuntimeException(
                "Tất cả AI providers đều thất bại:\n\n" . implode("\n\n", $errors) .
                "\n\nGroq Whisper cũng không khả dụng. Vui lòng kiểm tra:\n" .
                "- GROQ_API_KEY trong file .env (đã có: " . (!empty(config('gemini.groq.api_key')) ? 'YES' : 'NO') . ")\n" .
                "- File audio có hợp lệ không (định dạng, kích thước)\n" .
                "- Kết nối mạng đến api.groq.com"
            );
        }

        // Co transcript - dung Groq de tao summary/quiz/flashcard
        $this->updateStage($lecture, 'analyzing', 55, 'Đã có transcript - tạo nội dung bằng Groq AI...');

        if ($this->groq->isConfigured()) {
            try {
                $result = $this->groq->generateFromTranscript($transcript);
                $result['_provider'] = 'whisper+groq';
                Log::info('Processing succeed via Whisper + Groq', ['lecture_id' => $lecture->id]);
                return $result;
            } catch (\Throwable $e) {
                $errors[] = 'Groq: ' . $e->getMessage();
                Log::warning('Groq failed', ['error' => $e->getMessage()]);
            }
        } else {
            $errors[] = 'Groq: chưa cấu hình GROQ_API_KEY trong .env';
        }

        // ===== CACH 3: Co transcript nhung khong co Groq -> dung summary co ban =====
        Log::warning('Chi co transcript, summary/quiz se la placeholder');
        $result = $transcript + [
            'summary' => [
                'brief' => 'Không thể tạo tóm tắt vì các AI providers đều không khả dụng. ' .
                          'Vui lòng thêm GROQ_API_KEY vào .env để có kết quả đầy đủ.',
                'key_takeaways' => ['Vui lòng đăng ký Groq API key để có summary đầy đủ'],
                'topics' => [],
                'sentiment' => 'neutral',
            ],
            'quizzes' => [],
            'flashcards' => [],
            '_provider' => 'whisper-only',
        ];
        return $result;
    }

    /**
     * Lay transcript bang Whisper API.
     * Thu Groq Whisper truoc (mien phi, rat nhanh), neu khong co thi thu OpenAI.
     * Tra ve array co format {language, transcript: {...}} neu thanh cong.
     */
    protected function transcribeWithWhisperOrFallback(string $audioPath, Lecture $lecture): ?array
    {
        // 1) Thu Groq Whisper truoc (free + rat nhanh, support tieng Viet tot)
        $groqKey = (string) config('gemini.groq.api_key');
        if (!empty($groqKey)) {
            $this->updateStage($lecture, 'transcribing', 40, 'Đang dùng Groq Whisper (miễn phí)...');
            $result = $this->callWhisperApi(
                'https://api.groq.com/openai/v1/audio/transcriptions',
                'whisper-large-v3',
                [
                    'Authorization' => 'Bearer ' . $groqKey,
                ],
                $audioPath
            );
            if ($result !== null) {
                Log::info('Transcript via Groq Whisper OK', ['lecture_id' => $lecture->id]);
                return $result;
            }
        }

        // 2) Fallback OpenAI Whisper
        $openaiKey = (string) config('gemini.openai.api_key');
        if (empty($openaiKey)) {
            Log::info('Khong co OpenAI key, bo qua Whisper');
            return null;
        }

        $this->updateStage($lecture, 'transcribing', 40, 'Đang dùng OpenAI Whisper...');
        $model = (string) config('gemini.openai.whisper_model', 'whisper-1');
        return $this->callWhisperApi(
            'https://api.openai.com/v1/audio/transcriptions',
            $model,
            [
                'Authorization' => 'Bearer ' . $openaiKey,
            ],
            $audioPath
        );
    }

    /**
     * Goi Whisper API (chuan OpenAI-compatible) va tra ve transcript.
     */
    protected function callWhisperApi(string $url, string $model, array $headers, string $audioPath): ?array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(180)
                ->connectTimeout(15)
                ->withHeaders($headers)
                ->attach('file', file_get_contents($audioPath), basename($audioPath))
                ->attach('model', $model)
                ->attach('response_format', 'verbose_json')
                ->attach('language', 'vi')
                ->post($url);

            if (!$response->successful()) {
                Log::error('Whisper API failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 300),
                ]);
                return null;
            }

            $data = $response->json();
            $fullText = $data['text'] ?? '';
            if (empty($fullText)) {
                Log::warning('Whisper tra ve text rong');
                return null;
            }

            $segments = [];
            if (isset($data['segments']) && is_array($data['segments'])) {
                foreach ($data['segments'] as $seg) {
                    $segments[] = [
                        'start' => (float) ($seg['start'] ?? 0),
                        'end' => (float) ($seg['end'] ?? 0),
                        'text' => $seg['text'] ?? '',
                        'speaker' => 'unknown',
                        'speaker_label' => null,
                    ];
                }
            }

            return [
                'language' => $data['language'] ?? 'vi',
                'transcript' => [
                    'full_text' => $fullText,
                    'word_count' => str_word_count($fullText),
                    'segments' => $segments,
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('Whisper exception', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }
    }

    protected function saveTranscript(Lecture $lecture, array $data, string $language): void
    {
        DB::transaction(function () use ($lecture, $data, $language) {
            $transcript = $lecture->transcript()->updateOrCreate(
                ['lecture_id' => $lecture->id],
                [
                    'full_text' => $data['full_text'],
                    'language' => $language,
                    'word_count' => $data['word_count'],
                ]
            );

            $transcript->segments()->delete();

            foreach ($data['segments'] as $seg) {
                $transcript->segments()->create([
                    'start_time' => $seg['start'],
                    'end_time' => $seg['end'],
                    'text' => $seg['text'],
                    'speaker' => $seg['speaker'],
                    'speaker_label' => $seg['speaker_label'],
                ]);
            }
        });
    }

    protected function saveSummary(Lecture $lecture, array $data): void
    {
        $lecture->summary()->updateOrCreate(
            ['lecture_id' => $lecture->id],
            [
                'brief' => $data['brief'],
                'key_takeaways' => $data['key_takeaways'],
                'topics' => $data['topics'],
                'sentiment' => $data['sentiment'],
            ]
        );
    }

    protected function saveQuizzes(Lecture $lecture, array $quizzes): void
    {
        DB::transaction(function () use ($lecture, $quizzes) {
            $lecture->quizzes()->delete();

            foreach ($quizzes as $q) {
                $quiz = $lecture->quizzes()->create([
                    'question' => $q['question'],
                    'explanation' => $q['explanation'],
                    'difficulty' => $q['difficulty'],
                    'topic' => $q['topic'],
                    'correct_index' => $q['correct_index'],
                ]);

                foreach ($q['options'] as $idx => $optionText) {
                    $quiz->options()->create([
                        'text' => $optionText,
                        'index' => $idx,
                        'is_correct' => $idx === $q['correct_index'],
                    ]);
                }
            }
        });
    }

    protected function saveFlashcards(Lecture $lecture, array $flashcards): void
    {
        DB::transaction(function () use ($lecture, $flashcards) {
            $lecture->flashcards()->delete();

            foreach ($flashcards as $card) {
                $lecture->flashcards()->create([
                    'term' => $card['term'],
                    'definition' => $card['definition'],
                    'example' => $card['example'],
                ]);
            }
        });
    }

    protected function updateStage(Lecture $lecture, string $stage, int $progress, string $message): void
    {
        $lecture->update(['status' => $stage]);

        ProcessingJob::create([
            'lecture_id' => $lecture->id,
            'stage' => $stage,
            'progress' => $progress,
            'message' => $message,
            'started_at' => $stage === 'transcribing' ? now() : null,
            'finished_at' => in_array($stage, ['completed', 'failed']) ? now() : null,
        ]);
    }

    protected function convertToOgg(string $inputPath, string $mimeType): ?string
    {
        $ffmpegPath = config('gemini.ffmpeg_path', 'C:\\ffmpeg\\ffmpeg-master-latest-win64-gpl\\bin\\ffmpeg.exe');
        $outputPath = storage_path('app/temp_' . uniqid() . '.ogg');

        $extension = pathinfo($inputPath, PATHINFO_EXTENSION);

        $command = sprintf(
            '"%s" -y -i "%s" -vn -acodec libvorbis -q:a 4 "%s" 2>&1',
            $ffmpegPath,
            $inputPath,
            $outputPath
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputPath)) {
            Log::error('FFmpeg conversion failed', [
                'input' => $inputPath,
                'output' => implode("\n", $output),
                'return_code' => $returnCode,
            ]);
            return null;
        }

        return $outputPath;
    }
}