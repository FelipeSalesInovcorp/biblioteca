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
        Schema::create('requisicoes', function (Blueprint $table) {
            $table->id();

            // numeração sequencial (por agora simples; depois refinamos)
            $table->unsignedBigInteger('numero_sequencial')->unique();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();   // cidadão
            $table->foreignId('livro_id')->constrained()->cascadeOnDelete(); // livro

            $table->date('data_requisicao');
            $table->date('data_prevista_fim');   // +5 dias
            $table->date('data_entrega_real')->nullable();

            $table->timestamps();

             // evita duplicar requisição ativa do mesmo livro (opcional, validação vai fazer também)
            $table->index(['livro_id', 'data_entrega_real']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisicoes');
    }
};
