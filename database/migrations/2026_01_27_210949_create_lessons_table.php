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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            
            // Aqui está a mágica: O ID do Curso Pai
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            $table->string('title');       // Título da Aula (Ex: Aula 1 - Introdução)
            $table->string('video_url');   // Link do Youtube/Vimeo desta aula específica
            $table->integer('position')->default(0); // Para ordenar (Aula 1, 2, 3...)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
