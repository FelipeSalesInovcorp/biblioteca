<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('module');          // Ex: Requisicoes, Livros, Encomendas
            $table->unsignedBigInteger('object_id')->nullable();
            $table->text('change');            // descrição da ação
            $table->string('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps(); // created_at = data + hora
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
