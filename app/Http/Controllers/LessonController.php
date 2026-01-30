<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function store(Request $request, Course $course)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'required|url',
            'position' => 'nullable|integer' // Agora é 'nullable'
        ]);

        // Se não informou posição, pega a última + 1
        if (empty($validated['position'])) {
            $maxPosition = $course->lessons()->max('position') ?? 0;
            $validated['position'] = $maxPosition + 1;
        } else {
            // Se informou, verificamos se já existe
            $exists = $course->lessons()->where('position', $validated['position'])->exists();
            if ($exists) {
                // Opção A: Erro (Obriga o admin a corrigir)
                // return back()->withErrors(['position' => 'Já existe uma aula nesta posição.']);
                
                // Opção B: Empurra para o final (Mais fácil)
                $maxPosition = $course->lessons()->max('position') ?? 0;
                $validated['position'] = $maxPosition + 1;
            }
        }

        // Extrai o ID do Youtube
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $validated['video_url'], $matches);
        $videoId = $matches[1] ?? null;
        $validated['embed_url'] = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;

        $course->lessons()->create($validated);

        return back();
    }

    // Método para Marcar aula completada
    public function toggleComplete(Lesson $lesson)
    {
        // syncWithoutDetaching garante que se já existe, não duplica e nem remove
        auth()->user()->completedLessons()->syncWithoutDetaching([$lesson->id]);
        
        return back();
    }

    public function show(Course $course, Lesson $lesson)
    {
        // 1. O Porteiro: Verifica se o usuário tem matrícula
        // Se NÃO for Admin E NÃO estiver matriculado -> Bloqueia
        $isEnrolled = auth()->user()->enrolledCourses()->where('course_id', $course->id)->exists();
        
        if (!auth()->user()->is_admin && !$isEnrolled) {
            // Redireciona para a página de vendas ou dá erro
            return redirect()->route('courses.index')->with('error', 'Você precisa se matricular para assistir.');
            // Por enquanto volta para a Index, mas depois vai para o Checkout
        }

        // 2. Carrega todas as aulas do curso (para o menu e navegação)
        // E já verifica quais o usuário logado completou!
        $course->load(['lessons' => function ($query) {
            $query->orderBy('position', 'asc')
                  ->withExists(['users as is_completed' => function ($q) {
                      $q->where('user_id', auth()->id());
                  }]);
        }]);

        // 3. Lógica do Anterior / Próximo
        // Pega a lista de aulas ordenadas
        $lessons = $course->lessons;
        
        // Encontra o índice da aula atual na lista (0, 1, 2...)
        $currentIndex = $lessons->search(function($item) use ($lesson) {
            return $item->id === $lesson->id;
        });

        // 4. Define quem é quem
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < ($lessons->count() - 1) ? $lessons[$currentIndex + 1] : null;

        // 5. Verifica se a aula ATUAL está completa
        $isCompleted = auth()->user()->completedLessons()->where('lesson_id', $lesson->id)->exists();

        return Inertia::render('Lessons/Watch', [
            'course' => $course,
            'currentLesson' => $lesson,
            'isCompleted' => $isCompleted, // Enviamos para o Vue
            'prevLesson' => $prevLesson,   // Botão Anterior
            'nextLesson' => $nextLesson    // Botão Próximo
        ]);
    }

    // Formulário de Edição da Aula
    public function edit(Course $course, Lesson $lesson)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        return Inertia::render('Lessons/Edit', [
            'course' => $course,
            'lesson' => $lesson
        ]);
    }

    // Salvar alterações da Aula
    public function update(Request $request, Course $course, Lesson $lesson)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'required|url',
            'position' => 'required|integer'
        ]);

        // Recalcula o Embed se mudou a URL
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $validated['video_url'], $matches);
        $videoId = $matches[1] ?? null;
        $validated['embed_url'] = $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;

        $lesson->update($validated);

        // Volta para a lista de aulas (Gestão)
        return redirect()->route('courses.show', $course->id)->with('success', 'Aula atualizada!');
    }

    public function destroy(Course $course, Lesson $lesson)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        $lesson->delete();

        // Volta para a página do curso
        return redirect()->route('courses.show', $course->id)->with('success', 'Aula excluída!');
    }
}