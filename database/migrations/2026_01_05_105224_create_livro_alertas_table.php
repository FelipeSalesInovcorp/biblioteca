<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('livro_alertas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('livro_id')->constrained('livros')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // quando foi notificado (null = ainda pendente)
            $table->timestamp('notificado_em')->nullable();

            $table->timestamps();

            // 1 alerta pendente por utilizador por livro
            $table->unique(['livro_id', 'user_id']);
            $table->index(['livro_id', 'notificado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livro_alertas');
    }
};
