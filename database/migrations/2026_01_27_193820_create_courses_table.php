<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');              // Título do Curso
            $table->text('description');          // Descrição completa
            $table->string('video_url')->nullable(); // Link da aula (Youtube/Vimeo)
            $table->boolean('is_published')->default(false); // Rascunho ou Publicado?
            $table->timestamps();                 // Data de criação e atualização
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
