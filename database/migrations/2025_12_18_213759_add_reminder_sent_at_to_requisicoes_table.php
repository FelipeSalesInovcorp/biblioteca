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
        Schema::table('requisicoes', function (Blueprint $table) {
            // Adiciona a coluna 'reminder_enviado_em' do tipo timestamp, que pode ser nula, após a coluna 'data_entrega_real'
            $table->timestamp('reminder_enviado_em')->nullable()->after('data_entrega_real');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            // Remove a coluna 'reminder_enviado_em'
            $table->dropColumn('reminder_enviado_em');
        });
    }
};
