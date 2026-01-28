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

    public function show(Course $course, Lesson $lesson)
    {
        // Garante que carregamos TODAS as aulas do curso (para o menu lateral)
        // ordenadas pela posição
        $course->load(['lessons' => function ($query) {
            $query->orderBy('position', 'asc');
        }]);

        return Inertia::render('Lessons/Watch', [
            'course' => $course,
            'currentLesson' => $lesson,
        ]);
    }
}