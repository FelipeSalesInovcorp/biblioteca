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
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('titulo');
            $table->foreignId('editora_id')->constrained('editoras')->cascadeOnDelete();
            $table->text('bibliografia');
            $table->string('imagem_capa'); // Campo para armazenar o caminho da capa do livro
            $table->decimal('preco', 10, 2); // Campo para armazenar o preço do livro

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
