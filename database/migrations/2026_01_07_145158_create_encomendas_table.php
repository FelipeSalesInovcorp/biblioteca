<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // pendente | paga | cancelada
            $table->string('estado')->default('pendente');

            // Morada de entrega
            $table->string('nome_entrega');
            $table->string('morada');
            $table->string('codigo_postal', 20);
            $table->string('localidade');

            $table->decimal('total', 10, 2)->default(0);

            // Stripe
            $table->string('stripe_session_id')->nullable();

            // Data do pagamento
            $table->timestamp('pago_em')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
