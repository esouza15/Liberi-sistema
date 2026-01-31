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
    // 1. LISTAGEM (INDEX) - Mostra deletados para o Admin
    public function index()
    {
        $query = Course::query();

        // Se for admin, traz inclusive os deletados (Soft Deletes)
        if (auth()->user()->is_admin) {
            $query->withTrashed(); 
        } else {
            // Se não for admin, mostra apenas os publicados E ativos
            $query->where('is_published', true);
        }

        return Inertia::render('Courses/Index', [
            'courses' => $query->with('lessons')->get()
        ]);
    }

    /**
     * PAINEL DO ALUNO (Dashboard)
     */
    public function dashboard()
    {
        $user = auth()->user();

        // O withTrashed() traz os cursos ativos E os arquivados que o aluno comprou.
        $courses = $user->courses()->withTrashed()->get();

        return Inertia::render('Dashboard', [
            'courses' => $courses
        ]);
    }

    /**
     * GESTÃO DO CURSO (Show)
     */
    public function show($id)
    {
        // 1. Busca o curso (inclusive na lixeira)
        // E também busca as Aulas (inclusive na lixeira)
        $course = Course::withTrashed()
            ->with(['lessons' => function ($query) {
                $query->withTrashed()->orderBy('position');
            }])
            ->findOrFail($id);

        $user = auth()->user();

        // 2. VERIFICAÇÃO DE SEGURANÇA (IMPORTANTE!)
        // Se o curso estiver excluído (deleted_at não é nulo),
        // só permitimos o acesso se o usuário for ADMIN ou se for ALUNO MATRICULADO.
        if ($course->deleted_at) {
            $isEnrolled = $course->users()->where('user_id', $user->id)->exists();
            
            if (!$user->is_admin && !$isEnrolled) {
                // Se não é admin e não comprou, dá erro 404 (como se não existisse)
                abort(404);
            }
        }

        return Inertia::render('Courses/Show', [
            'course' => $course,
            // Verifica se o usuário atual já comprou este curso
            'isEnrolled' => $course->users()->where('user_id', $user->id)->exists()
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

    // 2. EDIÇÃO (EDIT) - Busca pelo ID manual para achar na lixeira
    public function edit($id)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        // withTrashed() permite editar um curso que está no "Arquivo Morto"
        $course = Course::withTrashed()->with('lessons')->findOrFail($id);

        return Inertia::render('Courses/Edit', [ // Ou 'Courses/Show' se você usa edição inline
            'course' => $course
        ]);
    }

    // 3. ATUALIZAÇÃO (UPDATE) - Permite salvar alterações em curso deletado
    public function update(Request $request, $id)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        $course = Course::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'video_url' => 'nullable|url',
            'image' => 'nullable|image|max:2048', // Validação de imagem
        ]);

        // Lógica de Upload de Imagem
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('courses', 'public');
            $validated['image_url'] = $path; // Salva o caminho novo
        }

        $course->update($validated);

        return redirect()->back()->with('success', 'Curso atualizado com sucesso!');
    }

    public function destroy(Course $course)
    {
        if (! auth()->user()->is_admin) { abort(403); }

        // --- MODO LEGACY (DIREITO ADQUIRIDO) ---
        
        // 1. NÃO usamos o detach().
        //$course->lessons()->delete();
        // A matrícula continua existindo no banco. O aluno antigo
        // ainda conseguirá acessar a rota /courses/{id} se tiver o link direto
        // ou através do histórico dele.

        // 2. Soft Delete nas aulas
        // As aulas são marcadas como excluídas, mas como o curso pai
        // também será excluído (soft), o Laravel entende o contexto.
        $course->lessons()->delete();

        // 3. NÃO apagamos os pedidos (Order).
        // O histórico financeiro fica 100% preservado.

        // 4. Soft Delete no Curso
        // Ele some das listas públicas (index), mas continua no banco.
        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Curso removido da Loja! Alunos antigos continuam com acesso.');
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