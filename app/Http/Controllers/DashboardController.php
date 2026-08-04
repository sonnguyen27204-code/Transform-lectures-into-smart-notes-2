<?php

namespace App\Http\Controllers;

use App\Models\Lecture;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $cacheKey = 'dashboard.stats.' . $user->id;

        $stats = Cache::remember($cacheKey, 60, function () use ($user) {
            return [
                'total' => $user->lectures()->count(),
                'completed' => $user->lectures()->where('status', 'completed')->count(),
                'processing' => $user->lectures()->whereIn('status', ['pending', 'uploading', 'transcribing', 'analyzing', 'generating'])->count(),
                'failed' => $user->lectures()->where('status', 'failed')->count(),
                'total_duration' => $user->lectures()->where('status', 'completed')->sum('duration'),
            ];
        });

        $recentLectures = $user->lectures()
            ->with(['transcript', 'summary'])
            ->withCount('quizzes')
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard.index', compact('stats', 'recentLectures'));
    }
}
