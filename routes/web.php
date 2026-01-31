<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

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

    // Opção de Deletar Cursos/Aulas
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::delete('/courses/{course}/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

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

/*
//Exibir erros de conexão com API ASAAS
Route::get('/diagnostico-asaas', function () {
    echo "<h1>Diagnóstico de Integração Asaas</h1>";
    echo "<hr>";

    // 1. VERIFICAR VARIÁVEIS DE AMBIENTE
    $apiKey = env('ASAAS_API_KEY');
    $apiUrl = env('ASAAS_API_URL');
    
    echo "<h3>1. Configuração (.env)</h3>";
    echo "<strong>URL:</strong> " . $apiUrl . "<br>";
    echo "<strong>Key detectada:</strong> " . ($apiKey ? "Sim (Inicia com: " . substr($apiKey, 0, 5) . "...)" : "<span style='color:red'>NÃO DETECTADA</span>") . "<br>";
    
    if (!$apiKey) {
        echo "<p style='color:red'>ERRO CRÍTICO: O Laravel não está lendo a ASAAS_API_KEY. Tente rodar a rota de limpeza de cache novamente.</p>";
        return;
    }

    // 2. TESTE DE CONEXÃO (PING)
    echo "<hr><h3>2. Teste de Conexão com Asaas</h3>";
    try {
        // Tenta listar clientes (rota leve) apenas para testar autenticação
        $response = Http::withHeaders(['access_token' => $apiKey])->get($apiUrl . '/customers?limit=1');
        
        if ($response->successful()) {
            echo "<span style='color:green'><strong>SUCESSO:</strong> Conexão estabelecida e Chave válida!</span><br>";
            echo "Status: " . $response->status();
        } else {
            echo "<span style='color:red'><strong>FALHA NA REQUISIÇÃO:</strong></span><br>";
            echo "Status: " . $response->status() . "<br>";
            echo "Retorno: " . $response->body();
        }
    } catch (\Exception $e) {
        echo "<span style='color:red'><strong>ERRO DE CONEXÃO:</strong> O servidor não conseguiu sair para a internet ou DNS falhou.</span><br>";
        echo "Erro: " . $e->getMessage();
    }

    // 3. LER LOGS DO LARAVEL (ÚLTIMAS 50 LINHAS)
    echo "<hr><h3>3. Últimos Erros no Log (storage/logs/laravel.log)</h3>";
    $logPath = storage_path('logs/laravel.log');
    
    if (File::exists($logPath)) {
        $logContent = File::get($logPath);
        // Pega as últimas 3000 caracteres
        $lastLogs = substr($logContent, -3000);
        echo "<pre style='background:#f4f4f4; padding:10px; border:1px solid #ccc; overflow:auto; max-height:400px;'>" . htmlspecialchars($lastLogs) . "</pre>";
    } else {
        echo "Arquivo de log não encontrado.";
    }
});
*/


/*
//apagar depois
Route::get('/resetar-pedidos', function () {
    \App\Models\Order::truncate(); // APAGA TODOS OS PEDIDOS DO BANCO
    return 'Pedidos apagados! Agora você pode tentar comprar novamente como se fosse a primeira vez.';
});
*/


/*
Route::get('/debug-aula/{courseId}/{lessonId}', function ($courseId, $lessonId) {
    echo "<h1>Diagnóstico de Edição de Aula</h1>";
    echo "<hr>";
    echo "<pre style='font-size: 14px; background: #f4f4f4; padding: 15px;'>";

    // 1. VERIFICAR USUÁRIO
    $user = auth()->user();
    if (!$user) {
        echo "❌ <strong>ERRO:</strong> Você não está logado.\n";
        return;
    }
    
    echo "👤 <strong>Usuário:</strong> " . $user->name . " (ID: " . $user->id . ")\n";
    echo "🔑 <strong>É Admin? (is_admin):</strong> [" . $user->is_admin . "] " . ($user->is_admin ? "✅ SIM" : "⛔ NÃO") . "\n";
    
    if (!$user->is_admin) {
        echo "\n⚠️ <strong>ALERTA:</strong> O Controller bloqueia quem não tem is_admin = 1.\n";
    }

    // 2. VERIFICAR CURSO
    $course = \App\Models\Course::find($courseId);
    if (!$course) {
        echo "❌ <strong>ERRO:</strong> Curso ID $courseId não encontrado no banco.\n";
    } else {
        echo "📚 <strong>Curso:</strong> " . $course->title . " (ID: " . $course->id . ")\n";
    }

    // 3. VERIFICAR AULA
    $lesson = \App\Models\Lesson::find($lessonId);
    if (!$lesson) {
        echo "❌ <strong>ERRO:</strong> Aula ID $lessonId não encontrada no banco.\n";
    } else {
        echo "📹 <strong>Aula:</strong> " . $lesson->title . " (ID: " . $lesson->id . ")\n";
        echo "   - Course ID da aula: " . $lesson->course_id . "\n";
        
        if ($course && $lesson->course_id != $course->id) {
            echo "⚠️ <strong>ALERTA DE VÍNCULO:</strong> Esta aula pertence ao curso " . $lesson->course_id . ", mas você tentou acessar pelo curso " . $course->id . ".\n";
        }
    }

    echo "</pre>";
    echo "<hr>";
    echo "<p>Se tudo acima estiver ✅, o problema é 100% no arquivo .vue (Frontend).</p>";
});
*/

Route::get('/fix-storage', function () {
    $target = storage_path('app/public');
    $link = public_path('storage');

    // Tenta rodar o comando nativo do Laravel
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        return 'Sucesso! Comando storage:link executado via Artisan.<br>' . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        // Se falhar, tenta criar o link simbólico manualmente (PHP puro)
        if (!file_exists($link)) {
             symlink($target, $link);
             return "Sucesso! Link simbólico criado manualmente via PHP.";
        } else {
             return "O link já existe ou não pôde ser criado. Erro: " . $e->getMessage();
        }
    }
});

require __DIR__.'/auth.php';