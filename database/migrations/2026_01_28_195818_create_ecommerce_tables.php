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
        // 1. Tabela de Matrículas (Quem tem acesso a quê)
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamp('enrolled_at')->useCurrent();
            
            // Garante que o aluno não se matricule 2x no mesmo curso
            $table->unique(['user_id', 'course_id']);
        });

        // 2. Tabela de Pedidos (O histórico financeiro)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('course_id')->constrained(); // Venda unitária por enquanto
            $table->decimal('amount', 10, 2); // Valor pago: 97.00
            $table->string('status')->default('pending'); // pending, paid, failed
            $table->string('payment_method')->nullable(); // pix, credit_card
            $table->string('transaction_id')->nullable(); // ID do Asaas/Gateway
            $table->timestamps();
        });

        // 3. Adicionar Preço aos Cursos
        Schema::table('courses', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0.00)->after('description');
            // Vamos adicionar uma URL de imagem de capa para ficar bonito na loja
            $table->string('image_url')->nullable()->after('price'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_tables');
    }
};
