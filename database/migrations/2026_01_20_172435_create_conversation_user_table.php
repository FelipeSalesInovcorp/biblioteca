<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_user', function (Blueprint $table) {
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('role', 20)->nullable(); // owner | member | moderator (opcional)
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('last_read_at')->nullable(); // rastrear mensagens não lidas

            $table->primary(['conversation_id', 'user_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_user');
    }
};
