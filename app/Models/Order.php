<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Libera a criação em massa desses campos
    protected $fillable = [
        'user_id', 
        'course_id', 
        'status', 
        'amount', 
        'transaction_id', 
        'qr_code_payload', 
        'qr_code_image'
    ];

    // Relação: Um pedido pertence a um Usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação: Um pedido pertence a um Curso
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}