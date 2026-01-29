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
     * Cria um novo cliente no Asaas
     */
    public function createCustomer($user)
    {
        // GERA UM CPF VÁLIDO ALEATÓRIO PARA TESTE
        // (Em produção, você usará o CPF real do usuário vindo do banco)
        $fakeCpf = $this->generateCpf();
        
        $response = $this->http->post('/customers', [
            'name' => $user->name,
            'email' => $user->email,
            'cpfCnpj' => $fakeCpf, 
        ]);

        return $response->json();
    }

    /**
     * Gera a cobrança Pix
     */
    public function createPixCharge($user, $amount, $description, $externalReference)
    {
        $customer = $this->createCustomer($user);
        $customerId = $customer['id'] ?? null;

        if (!$customerId) {
            // Se der erro, lança exceção com a mensagem do Asaas para aparecer no Log
            throw new \Exception('Erro ao criar cliente no Asaas: ' . json_encode($customer));
        }

        $response = $this->http->post('/payments', [
            'customer' => $customerId,
            'billingType' => 'PIX',
            'value' => $amount,
            'dueDate' => now()->addDays(1)->format('Y-m-d'),
            'description' => $description,
            'externalReference' => $externalReference,
        ]);

        return $response->json();
    }

    /**
     * Pega o QR Code
     */
    public function getPixQrCode($paymentId)
    {
        $response = $this->http->get("/payments/{$paymentId}/pixQrCode");
        return $response->json();
    }

    /**
     * FUNÇÃO AUXILIAR: Gera um CPF válido matematicamente para testes
     */
    private function generateCpf() {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - ($this->mod($d1, 11));
        if ($d1 >= 10) $d1 = 0;
        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - ($this->mod($d2, 11));
        if ($d2 >= 10) $d2 = 0;
        return ''.$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$d1.$d2;
    }

    private function mod($dividendo, $divisor) {
        return round($dividendo - (floor($dividendo / $divisor) * $divisor));
    }
}