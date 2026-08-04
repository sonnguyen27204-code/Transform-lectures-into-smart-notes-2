<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    /**
     * Submit đáp án quiz, tính điểm.
     */
    public function submit(Request $request, Quiz $quiz)
    {
        $lecture = $quiz->lecture;
        if ($lecture->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'selected_index' => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $isCorrect = (int) $data['selected_index'] === (int) $quiz->correct_index;
        $correctOption = $quiz->options()->where('index', $quiz->correct_index)->first();

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_index' => $quiz->correct_index,
            'correct_text' => $correctOption?->text,
            'explanation' => $quiz->explanation,
        ]);
    }
}
