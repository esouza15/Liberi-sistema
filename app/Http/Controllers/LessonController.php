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

    public function show($courseId, $lessonId)
    {
        // 1. Busca Curso e Aula (Permitindo itens da lixeira / Deletados)
        $course = \App\Models\Course::withTrashed()->findOrFail($courseId);
        
        $lesson = \App\Models\Lesson::withTrashed()
            ->where('course_id', $courseId)
            ->findOrFail($lessonId);

        $user = auth()->user();

        // 2. TRAVA DE SEGURANÇA PARA ITENS DELETADOS
        // Se o curso ou a aula foram deletados, precisamos verificar permissão rigorosa.
        if ($course->deleted_at || $lesson->deleted_at) {
            
            // Verifica se o aluno REALMENTE comprou esse curso
            $isEnrolled = $course->users()->where('user_id', $user->id)->exists();

            // Se não for Admin E não for Aluno Matriculado -> Bloqueia (404)
            if (!$user->is_admin && !$isEnrolled) {
                abort(404);
            }
        }

        // 3. Renderiza a aula
        return Inertia::render('Lessons/Watch', [
            'course' => $course,
            'lesson' => $lesson,
            // Na lista lateral, também trazemos as aulas deletadas para manter a sequência
            'lessons' => $course->lessons()->withTrashed()->orderBy('position')->get(),
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