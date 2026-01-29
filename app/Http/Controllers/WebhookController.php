<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Recebe os dados do Asaas
        $data = $request->all();
        $event = $data['event'] ?? null;
        $payment = $data['payment'] ?? null;

        // Log para depuração (opcional, bom para ver o que chega)
        Log::info('Webhook Asaas Recebido:', $data);

        // Se não tiver evento ou pagamento, ignora
        if (!$event || !$payment) {
            return response()->json(['status' => 'ignored'], 200);
        }

        // 2. Verifica se é um evento de Pagamento Confirmado
        if ($event === 'PAYMENT_RECEIVED' || $event === 'PAYMENT_CONFIRMED') {
            
            $transactionId = $payment['id'];

            // 3. Busca o Pedido no nosso banco pelo ID do Asaas
            $order = Order::where('transaction_id', $transactionId)->first();

            if ($order) {
                // Atualiza status do pedido
                $order->update(['status' => 'paid']);

                // 4. Libera o acesso ao curso (Matrícula)
                // Verifica se já não está matriculado para evitar erro
                $user = User::find($order->user_id);
                
                if ($user && !$user->enrolledCourses()->where('course_id', $order->course_id)->exists()) {
                    $user->enrolledCourses()->attach($order->course_id);
                    Log::info("Curso liberado para o usuário {$user->id} via Webhook.");
                }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}