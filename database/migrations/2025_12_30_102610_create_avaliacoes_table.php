<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('requisicao_id')->constrained('requisicoes')->cascadeOnDelete();
            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->unsignedTinyInteger('classificacao'); // 1..5
            $table->text('comentario');

            // suspensa | ativa | recusada
            $table->string('estado')->default('suspensa');
            $table->text('motivo_recusa')->nullable();

            $table->timestamps();

            //  avaliação por requisição
            $table->unique('requisicao_id');

            $table->index(['livro_id', 'estado']);
            $table->index(['user_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes');
    }
};
