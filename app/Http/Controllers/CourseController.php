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
        // Busca os cursos e conta quantas aulas cada um tem
        $courses = Course::withCount('lessons')->get();

        // Agora, para cada curso, vamos contar quantas o aluno já fez
        // (Usando a relação 'completedLessons' do Usuário que criamos antes)
        $userCompletedIds = auth()->user()->completedLessons()->pluck('lesson_id')->toArray();

        // Adiciona a contagem de concluídas em cada curso
        foreach ($courses as $course) {
            $course->completed_lessons_count = $course->lessons->whereIn('id', $userCompletedIds)->count();
            
            // Calcula a porcentagem já aqui para facilitar (0 a 100)
            if ($course->lessons_count > 0) {
                $course->progress_percent = round(($course->completed_lessons_count / $course->lessons_count) * 100);
            } else {
                $course->progress_percent = 0;
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
