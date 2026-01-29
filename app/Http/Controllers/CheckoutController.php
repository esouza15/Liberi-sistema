<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\AsaasService;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    // 1. Cria o Pedido (Ao clicar em "Comprar")
    public function store(Course $course, AsaasService $asaas)
    {
        if (!auth()->check()) { return redirect()->route('login'); }

        $user = auth()->user();

        // Verifica se já tem o curso (Matriculado)
        if ($user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course->id);
        }

        // --- CORREÇÃO AQUI ---
        // Verifica se já tem pedido pendente
        $pendingOrder = Order::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->first();

        // Só redireciona se o pedido pendente JÁ TIVER um QR Code gerado.
        // Se estiver pendente mas sem QR Code (falha anterior), ignoramos ele e criamos um novo.
        if ($pendingOrder && $pendingOrder->qr_code_payload) {
            return redirect()->route('checkout.show', $pendingOrder->id);
        }
        
        // Se tinha um pedido "morto" (sem QR code), podemos excluí-lo para não sujar o banco
        if ($pendingOrder) {
            $pendingOrder->delete();
        }
        // ---------------------

        // 1. Cria o Pedido Local (Rascunho)
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'amount' => $course->price,
            'status' => 'pending',
        ]);

        try {
            // 2. Chama o Asaas para gerar o Pix
            $charge = $asaas->createPixCharge(
                $user, 
                $course->price, 
                "Curso: " . $course->title, 
                $order->id
            );

            if (isset($charge['id'])) {
                // 3. Pega o QR Code
                $qrCodeData = $asaas->getPixQrCode($charge['id']);

                // 4. Atualiza o pedido com os dados do Asaas
                $order->update([
                    'transaction_id' => $charge['id'],
                    'qr_code_image' => $qrCodeData['encodedImage'],
                    'qr_code_payload' => $qrCodeData['payload'],
                ]);
            } else {
                Log::error('Erro Asaas Charge:', $charge);
            }

        } catch (\Exception $e) {
            Log::error('Erro Integração Asaas: ' . $e->getMessage());
        }

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