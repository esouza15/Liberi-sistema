<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    // Entradas permitidas.
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'is_published',
        'price',
        'image_url'
    ];

    // Imagem de capa dos curso
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // Se já for uma URL completa (ex: http...), retorna ela.
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // Se for caminho relativo, adiciona o /storage/ na frente
        return asset('storage/' . $value);
    }

    // Um curso tem muitas aulas.
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }

    // Alunos matriculados neste curso
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments');
    }
    
}