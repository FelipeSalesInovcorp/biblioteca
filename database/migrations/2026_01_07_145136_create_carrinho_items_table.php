<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carrinho_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('carrinho_id')
                ->constrained('carrinhos')
                ->cascadeOnDelete();

            $table->foreignId('livro_id')
                ->constrained('livros')
                ->cascadeOnDelete();

            $table->unsignedInteger('quantidade')->default(1);

            $table->decimal('preco_unitario', 10, 2);

            $table->timestamps();

            // 1 linha por livro no carrinho
            $table->unique(['carrinho_id', 'livro_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carrinho_items');
    }
};
