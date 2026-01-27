<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    // Um curso tem muitas aulas
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('position');
    }
}
