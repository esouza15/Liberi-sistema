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

    // Uma aula pertence a um curso
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}