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

    // Rota para editar 'lessons.show'
    Route::get('/courses/{course}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');

    // Rota para marcar como concluída (POST)
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'toggleComplete'])
    ->name('lessons.complete');


});

/*
// Rota temporária para corrigir o link de imagens
Route::get('/arrumar-imagens', function () {
    $targetFolder = storage_path('app/public');
    $linkFolder = public_path('storage');

    // 1. Tenta limpar o link antigo se existir (e estiver quebrado)
    if (file_exists($linkFolder)) {
        unlink($linkFolder); 
        echo "Link antigo removido... <br>";
    }

    // 2. Cria o novo link
    try {
        symlink($targetFolder, $linkFolder);
        return 'Sucesso! O atalho de imagens foi recriado. <br>Target: ' . $targetFolder . '<br>Link: ' . $linkFolder;
    } catch (\Exception $e) {
        return 'Erro: ' . $e->getMessage();
    }
});
*/


require __DIR__.'/auth.php';
