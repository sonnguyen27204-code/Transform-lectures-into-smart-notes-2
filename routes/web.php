<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LectureController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Guest
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Authenticated
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lectures
    Route::get('/lectures', [LectureController::class, 'index'])->name('lectures.index');
    Route::get('/lectures/create', [LectureController::class, 'create'])->name('lectures.create');
    Route::post('/lectures', [LectureController::class, 'store'])->name('lectures.store');
    Route::get('/lectures/{lecture}', [LectureController::class, 'show'])->name('lectures.show');
    Route::delete('/lectures/{lecture}', [LectureController::class, 'destroy'])->name('lectures.destroy');
    Route::get('/lectures/{lecture}/status', [LectureController::class, 'status'])->name('lectures.status');

    // Quiz
    Route::post('/quizzes/{quiz}/submit', [QuizController::class, 'submit'])->name('quizzes.submit');
});
