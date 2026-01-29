<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    // 1. Cria o Pedido (Ao clicar em "Comprar")
    public function store(Course $course)
    {
        // Garante que o usuário está logado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Verifica se já tem o curso
        if ($user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course->id);
        }

        // Verifica se já tem um pedido PENDENTE para esse curso (para não duplicar)
        $pendingOrder = Order::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingOrder) {
            // Se já tem pedido pendente, vai direto para o pagamento dele
            return redirect()->route('checkout.show', $pendingOrder->id);
        }

        // --- AQUI ENTRARÁ A INTEGRAÇÃO COM ASAAS FUTURAMENTE ---
        // Por enquanto, criamos apenas o registro no banco local
        
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'status' => 'pending',
            // 'qr_code_payload' => ... (virá do Asaas depois)
        ]);

        return redirect()->route('checkout.show', $order->id);
    }

    // 2. Mostra a Tela de Pagamento (Pix)
    public function show(Order $order)
    {
        // Segurança: só o dono do pedido pode ver
        if ($order->user_id !== auth()->id()) { abort(403); }
        
        // Carrega dados do curso para mostrar na tela
        $order->load('course');

        return Inertia::render('Checkout/Payment', [
            'order' => $order
        ]);
    }
}