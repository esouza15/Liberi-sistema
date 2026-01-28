<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Inertia\Inertia;

use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. Carregamos os cursos COM as aulas (para saber a ordem) e a contagem
        $courses = Course::with('lessons') // <--- Carregamos as aulas aqui
                        ->withCount('lessons')
                        ->get();

        // 2. Pegamos os IDs das aulas que o usuário JÁ fez
        $userCompletedIds = auth()->user()->completedLessons()->pluck('lesson_id')->toArray();

        foreach ($courses as $course) {
            // Contagem de progresso (igual antes)
            $course->completed_lessons_count = $course->lessons->whereIn('id', $userCompletedIds)->count();
            
            if ($course->lessons_count > 0) {
                $course->progress_percent = round(($course->completed_lessons_count / $course->lessons_count) * 100);
            } else {
                $course->progress_percent = 0;
            }

            // --- A MÁGICA DO "CONTINUAR" COMEÇA AQUI ---
            
            // Procura a primeira aula que NÃO está na lista de concluídas
            $nextLesson = $course->lessons
                ->whereNotIn('id', $userCompletedIds)
                ->sortBy('position')
                ->first();

            // Lógica de Destino:
            if ($nextLesson) {
                // Se tem aula pendente, o link vai direto para o Player da aula
                $course->target_route = route('lessons.show', [$course->id, $nextLesson->id]);
            } elseif ($course->lessons->count() > 0) {
                // Se já acabou tudo, manda para a primeira aula (para revisar) ou para a grade
                $course->target_route = route('courses.show', $course->id);
            } else {
                // Se o curso não tem aulas, manda para a grade (para o admin cadastrar)
                $course->target_route = route('courses.show', $course->id);
            }
        }

        return Inertia::render('Courses/Index', [
            'courses' => $courses
        ]);
    }

// Mostra a tela de criar curso
    public function create()
    {   
        // bloquear acesso não autorizado
        if (! auth()->user()->is_admin) {
            abort(403, 'Acesso não autorizado');
        }

        return Inertia::render('Courses/Create');
    }

    // Recebe os dados do formulário e salva no banco
    public function store(Request $request)
    {
        // 0. bloquear acesso não autorizado
        if (! auth()->user()->is_admin) {
            abort(403, 'Acesso não autorizado');
        }

        // 1. Validação (Segurança)
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'video_url' => 'nullable|url'
        ]);

        // 2. Salvar no Banco
        Course::create($validated);

        // 3. Voltar para a lista com mensagem de sucesso
        return redirect()->route('courses.index');
    }


    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        // Carrega o curso JUNTAMENTE com as aulas (ordenadas)
        $course->load(['lessons' => function ($query) {
            $query->orderBy('position', 'asc');
        }]);

        return Inertia::render('Courses/Show', [
            'course' => $course
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
