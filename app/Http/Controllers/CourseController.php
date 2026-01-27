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
    // 1. Busca todos os cursos no banco
    $courses = Course::all();

    // 2. Manda para a tela 'Courses/Index' enviando a lista junto
    return Inertia::render('Courses/Index', [
        'courses' => $courses
    ]);
}

// Mostra a tela de criar curso
    public function create()
    {
        return Inertia::render('Courses/Create');
    }

    // Recebe os dados do formulário e salva no banco
    public function store(Request $request)
    {
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
