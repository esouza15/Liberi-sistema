<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Inertia\Inertia; //<<<--- descomentar se for preciso.

class LessonController extends Controller
{
    public function store(Request $request, Course $course)
    {
        // 1. Validação
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'required|url',
            'position' => 'integer'
        ]);

        // 2. Cria a aula DENTRO do curso
        $course->lessons()->create($validated);

        // 3. Recarrega a página para ver a aula nova na lista
        return back();

        // 4. Bloqueia se não for admin
        if (! auth()->user()->is_admin) {
            abort(403, 'Acesso negado.');
        }
    }

    // Método para Marcar/Desmarcar aula
    public function toggleComplete(Lesson $lesson)
    {
        // Se já completou, remove. Se não, adiciona. (Toggle)
        auth()->user()->completedLessons()->toggle($lesson->id);
        
        return back(); // Recarrega a página
    }

    public function show(Course $course, Lesson $lesson)
    {
        // 1. Carrega todas as aulas do curso (para o menu e navegação)
        // E já verifica quais o usuário logado completou!
        $course->load(['lessons' => function ($query) {
            $query->orderBy('position', 'asc')
                  ->withExists(['users as is_completed' => function ($q) {
                      $q->where('user_id', auth()->id());
                  }]);
        }]);

        // 2. Lógica do Anterior / Próximo
        // Pega a lista de aulas ordenadas
        $lessons = $course->lessons;
        
        // Encontra o índice da aula atual na lista (0, 1, 2...)
        $currentIndex = $lessons->search(function($item) use ($lesson) {
            return $item->id === $lesson->id;
        });

        // Define quem é quem
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < ($lessons->count() - 1) ? $lessons[$currentIndex + 1] : null;

        // 3. Verifica se a aula ATUAL está completa
        $isCompleted = auth()->user()->completedLessons()->where('lesson_id', $lesson->id)->exists();

        return Inertia::render('Lessons/Watch', [
            'course' => $course,
            'currentLesson' => $lesson,
            'isCompleted' => $isCompleted, // Enviamos para o Vue
            'prevLesson' => $prevLesson,   // Botão Anterior
            'nextLesson' => $nextLesson    // Botão Próximo
        ]);
    }
}