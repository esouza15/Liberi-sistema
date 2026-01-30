<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Entradas permitidas.
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'is_published',
        'price',
        'image_url'
    ];

    // Um curso tem muitas aulas.
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    public function users()
    {
        // Indica que o curso pertence a muitos usuários (através da tabela pivô course_user)
        return $this->belongsToMany(User::class, 'course_user');
    }

    // Alunos matriculados neste curso
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }
    
}