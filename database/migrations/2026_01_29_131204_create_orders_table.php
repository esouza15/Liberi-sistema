<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            
            // Status do pagamento: pending (pendente), paid (pago), failed (falhou)
            $table->string('status')->default('pending');
            
            // Valor salvo no momento da compra (importante caso você mude o preço depois)
            $table->decimal('amount', 10, 2);
            
            // Campos futuros para o Asaas (Pix)
            $table->string('transaction_id')->nullable(); // ID do pedido no Asaas
            $table->text('qr_code_payload')->nullable();  // O código "copia e cola"
            $table->string('qr_code_image')->nullable();  // O link da imagem do QR
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
