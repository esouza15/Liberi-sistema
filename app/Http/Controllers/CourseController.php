<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson; // Adicionei isso por precaução
use Inertia\Inertia;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * CATÁLOGO DE CURSOS (Index)
     */
    public function index()
    {
        // 1. Inicia a query básica
        $query = Course::withCount('lessons');

        // 2. Se estiver logado, verifica matrícula
        if (auth()->check()) {
            $query->withExists(['students as is_enrolled' => function ($q) {
                $q->where('user_id', auth()->id());
            }]);
        }

        $courses = $query->get();
        
        // 3. Prepara dados auxiliares (apenas se logado)
        $userCompletedIds = [];
        if (auth()->check()) {
            $userCompletedIds = auth()->user()->completedLessons()->pluck('lesson_id')->toArray();
        }

        foreach ($courses as $course) {
            // Se NÃO estiver logado, define padrões de visitante
            if (!auth()->check()) {
                $course->is_enrolled = false;
                $course->progress_percent = 0;
                $course->target_route = route('courses.public', $course->id); // Manda pra Landing Page
            } 
            else {
                // Lógica de Aluno Logado (igual tínhamos antes)
                $course->completed_lessons_count = $course->lessons->whereIn('id', $userCompletedIds)->count();
                
                $course->progress_percent = ($course->lessons_count > 0) 
                    ? round(($course->completed_lessons_count / $course->lessons_count) * 100) 
                    : 0;
                
                // Se não tem o campo is_enrolled vindo do banco (admin as vezes não vem), checamos manual
                if (!isset($course->is_enrolled)) {
                     // Fallback simples
                     $course->is_enrolled = auth()->user()->enrolledCourses()->where('course_id', $course->id)->exists();
                }

                if ($course->is_enrolled || auth()->user()->is_admin) {
                    // Vai para a aula
                    $nextLesson = $course->lessons
                        ->whereNotIn('id', $userCompletedIds)
                        ->sortBy('position')
                        ->first();
                    
                    if (auth()->user()->is_admin) {
                        $course->target_route = route('courses.show', $course->id);
                    } elseif ($nextLesson) {
                        $course->target_route = route('lessons.show', [$course->id, $nextLesson->id]);
                    } else {
                        $course->target_route = route('courses.show', $course->id);
                    }
                } else {
                    // Logado mas não comprou -> Landing Page
                    $course->target_route = route('courses.public', $course->id);
                }
            }
            
            // Correção da imagem
            if ($course->image_url && !str_starts_with($course->image_url, '/storage')) {
                $course->image_url = '/storage/' . $course->image_url;
            }
        }

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'isLoggedIn' => auth()->check() // Informa ao Vue se tem login
        ]);
    }

    /**
     * PAINEL DO ALUNO (Dashboard)
     */
    public function dashboard()
    {
        $courses = Course::with('lessons')
                        ->withCount('lessons')
                        ->get();

        $userCompletedIds = auth()->user()->completedLessons()->pluck('lesson_id')->toArray();
        // 1. Pegamos a lista de IDs dos cursos que o aluno COMPROU
        $enrolledCourseIds = auth()->user()->enrolledCourses()->pluck('course_id')->toArray();

        foreach ($courses as $course) {
            $course->completed_lessons_count = $course->lessons->whereIn('id', $userCompletedIds)->count();
            
            if ($course->lessons_count > 0) {
                $course->progress_percent = round(($course->completed_lessons_count / $course->lessons_count) * 100);
            } else {
                $course->progress_percent = 0;
            }

            // Verifica se está matriculado
            $course->is_enrolled = in_array($course->id, $enrolledCourseIds);

            // --- DEFINIÇÃO INTELIGENTE DA ROTA ---
            if ($course->is_enrolled) {
                // CENÁRIO A: É ALUNO -> Vai para a próxima aula
                $nextLesson = $course->lessons
                    ->whereNotIn('id', $userCompletedIds)
                    ->sortBy('position')
                    ->first();

                if ($nextLesson) {
                    $course->target_route = route('lessons.show', [$course->id, $nextLesson->id]);
                } else {
                    $course->target_route = route('courses.show', $course->id);
                }
            } else {
                // CENÁRIO B: NÃO É ALUNO -> Vai para a Página de Vendas (Landing Page)
                // Isso elimina o clique intermediário no catálogo!
                $course->target_route = route('courses.public', $course->id);
            }

            // Correção da Imagem
            if ($course->image_url && !str_starts_with($course->image_url, '/storage')) {
                $course->image_url = '/storage/' . $course->image_url;
            }
        }

        return Inertia::render('Dashboard', [
            'courses' => $courses
        ]);
    }

    /**
     * GESTÃO DO CURSO (Show)
     */
    public function show(Course $course)
    {
        $course->load(['lessons' => function ($query) {
            $query->orderBy('position', 'asc');
        }]);

        if ($course->image_url && !str_starts_with($course->image_url, '/storage')) {
             $course->image_url = '/storage/' . $course->image_url;
        }

        return Inertia::render('Courses/Show', [
            'course' => $course
        ]);
    }

    /**
     * MÉTODOS DE ADMINISTRAÇÃO (Create, Store, Edit, Update)
     */
    public function create()
    {   
        if (! auth()->user()->is_admin) { abort(403); }
        return Inertia::render('Courses/Create');
    }

    public function store(Request $request)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
            $validated['image_url'] = $path;
        }
        unset($validated['image']);

        Course::create($validated);

        return redirect()->route('courses.index');
    }

    public function edit(Course $course)
    {
        // Redireciona para o Show, pois unificamos a gestão lá
        return redirect()->route('courses.show', $course->id);
    }

    public function update(Request $request, Course $course)
    {
        if (! auth()->user()->is_admin) { abort(403); }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
            $validated['image_url'] = $path;
        }
        unset($validated['image']);

        $course->update($validated);

        return redirect()->route('courses.show', $course->id)->with('success', 'Curso atualizado!');
    }

    public function destroy(string $id)
    {
        // Implementar futuramente se desejar deletar cursos
    }

    /**
     * PÁGINA DE VENDAS (PÚBLICA)
     */
    public function showPublic(Course $course)
    {
        // Carrega as aulas (apenas títulos e ordem) para mostrar na grade curricular
        $course->load(['lessons' => function ($query) {
            $query->orderBy('position', 'asc');
        }]);

        // Garante que a imagem tenha o caminho correto
        if ($course->image_url && !str_starts_with($course->image_url, '/storage')) {
             $course->image_url = '/storage/' . $course->image_url;
        }

        // Verifica se o usuário atual (se estiver logado) já tem o curso
        $userHasCourse = false;
        if (auth()->check()) {
            $userHasCourse = auth()->user()->enrolledCourses()->where('course_id', $course->id)->exists();
        }

        return Inertia::render('Courses/PublicShow', [
            'course' => $course,
            'userHasCourse' => $userHasCourse,
            'isLoggedIn' => auth()->check()
        ]);
    }
}