<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Inertia\Inertia;

use Illuminate\Http\Request;

class CourseController extends Controller
{

public function index()
    {
        // 1. Buscamos cursos + Contagem de aulas + VERIFICAÇÃO SE É ALUNO (is_enrolled)
        $courses = Course::withCount('lessons')
            ->withExists(['students as is_enrolled' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->get();

        // 2. IDs das aulas que o usuário JÁ fez
        $userCompletedIds = auth()->user()->completedLessons()->pluck('lesson_id')->toArray();

        foreach ($courses as $course) {
            // Contagem de progresso
            $course->completed_lessons_count = $course->lessons->whereIn('id', $userCompletedIds)->count();
            
            $course->progress_percent = ($course->lessons_count > 0) 
                ? round(($course->completed_lessons_count / $course->lessons_count) * 100) 
                : 0;

            // Lógica de Destino:
            // SE FOR ADMIN: Vai sempre para a Tela de Gestão (onde adiciona aulas)
            if (auth()->user()->is_admin) {
                $course->target_route = route('courses.show', $course->id);
            } 
            // SE FOR ALUNO: Mantém a lógica inteligente de "Continuar Assistindo"
            elseif ($nextLesson) {
                $course->target_route = route('lessons.show', [$course->id, $nextLesson->id]);
            } elseif ($course->lessons->count() > 0) {
                $course->target_route = route('courses.show', $course->id);
            } else {
                $course->target_route = route('courses.show', $course->id);
            }
            
            // 3. SUBIR DA IMAGEM
            if ($course->image_url) {
                $course->image_url = '/storage/' . $course->image_url;
            }
        }

        return Inertia::render('Courses/Index', [
            'courses' => $courses
        ]);
    }

    
public function dashboard()
    {
        // Reaproveitando a lógica inteligente de progresso
        $courses = Course::with('lessons')
                        ->withCount('lessons')
                        ->get();

        $userCompletedIds = auth()->user()->completedLessons()->pluck('lesson_id')->toArray();

        foreach ($courses as $course) {
            $course->completed_lessons_count = $course->lessons->whereIn('id', $userCompletedIds)->count();
            
            // Calcula porcentagem
            if ($course->lessons_count > 0) {
                $course->progress_percent = round(($course->completed_lessons_count / $course->lessons_count) * 100);
            } else {
                $course->progress_percent = 0;
            }

            // Define destino (Vídeo ou Capa)
            $nextLesson = $course->lessons
                ->whereNotIn('id', $userCompletedIds)
                ->sortBy('position')
                ->first();

            if ($nextLesson) {
                $course->target_route = route('lessons.show', [$course->id, $nextLesson->id]);
            } else {
                $course->target_route = route('courses.show', $course->id);
            }
        }

        // Renderiza a Dashboard enviando os cursos
        return Inertia::render('Dashboard', [
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

    public function store(Request $request)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric', // Validar preço
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validar imagem
            'video_url' => 'nullable|url'
        ]);

        // Upload da Imagem
        if ($request->hasFile('image')) {
            // Salva na pasta 'courses' dentro do disco public
            $path = $request->file('image')->store('courses', 'public');
            $validated['image_url'] = $path;
        }

        // Remove o campo 'image' do array (o banco espera 'image_url')
        unset($validated['image']);

        Course::create($validated);

        return redirect()->route('courses.index');
    }


    public function show(Course $course)
    {
        // Carrega aulas
        $course->load(['lessons' => function ($query) {
            $query->orderBy('position', 'asc');
        }]);

        // Garante que a imagem apareça corretamente na gestão também
        if ($course->image_url && !str_starts_with($course->image_url, '/storage')) {
             $course->image_url = '/storage/' . $course->image_url;
        }

        return Inertia::render('Courses/Show', [
            'course' => $course
        ]);
    }

    // Exibe o formulário de edição
    public function edit(Course $course)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        // Corrige o caminho da imagem para exibição
        if ($course->image_url && !str_starts_with($course->image_url, 'http')) {
            $course->image_url = '/storage/' . $course->image_url;
        }

        return Inertia::render('Courses/Edit', [
            'course' => $course
        ]);
    }

    // Salva as alterações
    public function update(Request $request, Course $course)
    {
        if (! auth()->user()->is_admin) { abort(403); }
        
        // Validação (igual ao anterior)
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

        // MUDANÇA AQUI: Volta para a mesma tela (Show) em vez da Index
        return redirect()->route('courses.show', $course->id)->with('success', 'Curso atualizado!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
