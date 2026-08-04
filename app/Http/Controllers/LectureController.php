<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use App\Jobs\ProcessLectureJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LectureController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->lectures()->with(['transcript', 'summary']);

        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        $lectures = $query->withCount('quizzes')->latest()->paginate(12)->withQueryString();

        return view('lectures.index', compact('lectures'));
    }

    public function create()
    {
        return view('lectures.create');
    }

    /**
     * Upload file audio + tạo record Lecture.
     * Dispatch queue job để xử lý AI async (background).
     */
    public function store(Request $request)
    {
        $maxKb = (int) config('gemini.audio.max_size_kb', 51200);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'language' => ['nullable', 'in:vi,en,auto'],
            'audio' => [
                'required', 'file',
                'mimetypes:' . implode(',', config('gemini.audio.allowed_mimes')),
                'max:' . $maxKb,
            ],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề bài giảng.',
            'audio.required' => 'Vui lòng chọn file âm thanh.',
            'audio.mimetypes' => 'File phải là định dạng âm thanh (mp3, wav, m4a, ogg...).',
            'audio.max' => 'File vượt quá ' . round($maxKb / 1024, 1) . ' MB.',
        ]);

        $file = $data['audio'];
        $path = $file->store('lectures/' . date('Y/m'), 'public');

        $lecture = Lecture::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'audio_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'language' => $data['language'] ?? 'vi',
            'status' => 'pending',
        ]);

        // Dispatch queue job xử lý AI ở background
        ProcessLectureJob::dispatch($lecture->id);

        return redirect()
            ->route('lectures.show', $lecture)
            ->with('success', 'Bài giảng đã được tải lên. AI đang xử lý trong nền...');
    }

    public function show(Lecture $lecture)
    {
        // Đảm bảo user chỉ xem được bài giảng của mình
        if ($lecture->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền truy cập bài giảng này.');
        }

        $lecture->load([
            'transcript.segments',
            'summary',
            'quizzes.options',
            'flashcards',
            'processingJobs' => fn($q) => $q->latest()->limit(20),
        ]);

        return view('lectures.show', compact('lecture'));
    }

    public function destroy(Lecture $lecture)
    {
        if ($lecture->user_id !== Auth::id()) {
            abort(403);
        }

        if ($lecture->audio_path && Storage::disk('public')->exists($lecture->audio_path)) {
            Storage::disk('public')->delete($lecture->audio_path);
        }

        $lecture->delete();

        return redirect()->route('lectures.index')->with('success', 'Đã xóa bài giảng.');
    }

    /**
     * API: lấy trạng thái xử lý realtime (poll mỗi 3s).
     */
    public function status(Lecture $lecture)
    {
        if ($lecture->user_id !== Auth::id()) {
            abort(403);
        }

        $latestJob = $lecture->processingJobs()->latest()->first();

        return response()->json([
            'status' => $lecture->status,
            'status_label' => $lecture->getStatusLabel(),
            'progress' => $latestJob->progress ?? 0,
            'message' => $latestJob->message ?? '',
            'is_completed' => $lecture->isCompleted(),
            'is_failed' => $lecture->isFailed(),
            'error_message' => $lecture->error_message,
        ]);
    }
}