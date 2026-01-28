<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    // Quais campos podem ser preenchidos no banco
    protected $fillable = [
        'course_id',
        'title',
        'video_url',
        'position'
    ];

    // 1. Avisa o Laravel que existe um campo "virtual" chamado embed_url
    protected $appends = ['embed_url'];

    // 2. Processo que cria esse campo automaticamente
    public function getEmbedUrlAttribute()
    {
        // Se não tiver link, retorna vazio
        if (!$this->video_url) return null;

        // Tenta achar o código do vídeo (ex: dQw4w9WgXcQ)
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches);

        // Se achou, retorna o link de embed. Se não, retorna o link original.
        return isset($matches[1]) 
            ? 'https://www.youtube.com/embed/' . $matches[1] 
            : $this->video_url;
    }

    // Uma aula pertence a um curso
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Relação inversa: Quem são os usuários que completaram esta aula?
    public function users()
    {
        return $this->belongsToMany(User::class, 'lesson_user')->withTimestamps();
    }
}