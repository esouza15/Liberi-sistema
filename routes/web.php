<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// --- ROTAS PÚBLICAS (Acessíveis a todos) ---

// 1. A Home é o Catálogo
Route::get('/', [CourseController::class, 'index'])->name('home');

// 2. O Catálogo também responde por /courses
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');

// 3. A Página de Vendas (Landing Page)
Route::get('/curso/{course}', [CourseController::class, 'showPublic'])->name('courses.public');

// --- ROTAS PROTEGIDAS (Apenas Logados) ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard do Aluno
    Route::get('/dashboard', [CourseController::class, 'dashboard'])->name('dashboard');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- GESTÃO DE CURSOS (ADMIN) E AULAS ---
    
    // Rotas de criação/edição de cursos (Admin)
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    
    // Visualização interna do curso (Player/Gestão)
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Aulas
    Route::post('/courses/{course}/lessons', [LessonController::class, 'store'])->name('courses.lessons.store');
    Route::get('/courses/{course}/lessons/{lesson}', [LessonController::class, 'show'])->name('lessons.show');
    Route::get('/courses/{course}/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('lessons.edit');
    Route::put('/courses/{course}/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::post('/lessons/{lesson}/complete', [LessonController::class, 'toggleComplete'])->name('lessons.complete');

    // Checkout
    Route::post('/course/{course}/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{order}', [CheckoutController::class, 'show'])->name('checkout.show');
});

/*
// --- ROTA TEMPORÁRIA DE MANUTENÇÃO (APAGAR DEPOIS) ---
Route::get('/limpar-tudo-123', function () {
    // Limpa o cache de configuração (Obrigatório para ler o .env novo)
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    
    // Limpa o cache da aplicação (Opcional, mas bom garantir)
    \Illuminate\Support\Facades\Artisan::call('cache:clear');

    return 'Configuração limpa! O Laravel agora está lendo as chaves novas do .env. <br> Pode apagar esta rota.';
});
*/

require __DIR__.'/auth.php';