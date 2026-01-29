<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AsaasService
{
    protected $http;

    public function __construct()
    {
        $this->http = Http::withHeaders([
            'access_token' => env('ASAAS_API_KEY'),
            'Content-Type' => 'application/json',
        ])->baseUrl(env('ASAAS_API_URL'));
    }

    /**
     * Cria um novo cliente no Asaas (Se necessário)
     */
    public function createCustomer($user)
    {
        // Tenta buscar cliente pelo email/CPF antes de criar (opcional, aqui faremos simples)
        // O ideal é salvar o asaas_customer_id na tabela users para não duplicar.
        
        $response = $this->http->post('/customers', [
            'name' => $user->name,
            'email' => $user->email,
            'cpfCnpj' => '00000000000', // Futuramente você pedirá o CPF no cadastro
        ]);

        return $response->json();
    }

    /**
     * Gera a cobrança Pix
     */
    public function createPixCharge($user, $amount, $description, $externalReference)
    {
        // 1. Precisamos de um Customer ID. 
        // Em produção, você salvaria isso no user. Aqui vamos criar um "on the fly" ou usar um fixo de teste.
        $customer = $this->createCustomer($user);
        $customerId = $customer['id'] ?? null;

        if (!$customerId) {
            throw new \Exception('Erro ao criar cliente no Asaas: ' . json_encode($customer));
        }

        // 2. Cria a cobrança
        $response = $this->http->post('/payments', [
            'customer' => $customerId,
            'billingType' => 'PIX',
            'value' => $amount,
            'dueDate' => now()->addDays(1)->format('Y-m-d'), // Vence amanhã
            'description' => $description,
            'externalReference' => $externalReference, // Nosso ID do pedido (Order ID)
        ]);

        return $response->json();
    }

    /**
     * Pega o QR Code e o Copia e Cola pelo ID da cobrança
     */
    public function getPixQrCode($paymentId)
    {
        $response = $this->http->get("/payments/{$paymentId}/pixQrCode");
        return $response->json();
    }
}