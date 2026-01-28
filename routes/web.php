<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [CourseController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rotas de Curso
    Route::resource('courses', CourseController::class);

    // Rota para Adicionar Aula (Repare que é POST e usa {course})
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])
    ->name('courses.lessons.store');

    // Rota para assistir a aula (GET)
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])
    ->name('lessons.show');

    // Rota para marcar como concluída (POST)
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'toggleComplete'])
    ->name('lessons.complete');
});


require __DIR__.'/auth.php';
