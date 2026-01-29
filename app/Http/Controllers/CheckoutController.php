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

        // Verifica se já tem o curso
        if ($user->enrolledCourses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('courses.show', $course->id);
        }

        // Verifica se já tem pedido pendente
        $pendingOrder = Order::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingOrder) {
            return redirect()->route('checkout.show', $pendingOrder->id);
        }

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
                    'transaction_id' => $charge['id'], // ID do pagamento no Asaas
                    'qr_code_image' => $qrCodeData['encodedImage'], // Imagem Base64
                    'qr_code_payload' => $qrCodeData['payload'], // Copia e Cola
                ]);
            } else {
                Log::error('Erro Asaas Charge:', $charge);
            }

        } catch (\Exception $e) {
            Log::error('Erro Integração Asaas: ' . $e->getMessage());
            // Em produção, mostraria um erro amigável.
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