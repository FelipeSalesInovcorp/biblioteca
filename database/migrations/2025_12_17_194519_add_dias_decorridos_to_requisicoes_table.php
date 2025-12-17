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
            // Adiciona a coluna 'dias_decorridos' após 'data_entrega_real'
            
            $table->unsignedInteger('dias_decorridos')->nullable()->after('data_entrega_real');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requisicoes', function (Blueprint $table) {
            //

            $table->dropColumn('dias_decorridos');
        });
    }
};
