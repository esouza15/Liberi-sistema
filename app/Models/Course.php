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
        'is_published'
    ];

    // Um curso tem muitas aulas.
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }
}