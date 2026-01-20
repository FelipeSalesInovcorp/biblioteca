<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Se não for sqlite, faz a alteração normal (MySQL etc.)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('livros', function ($table) {
                $table->decimal('preco', 10, 2)->nullable()->change();
            });
            return;
        }

        // SQLITE: recriar a tabela (workaround)

        $temNome = Schema::hasColumn('livros', 'nome');
        $temTitulo = Schema::hasColumn('livros', 'titulo');

        $colNomeAtual = $temNome ? 'nome' : ($temTitulo ? 'titulo' : null);

        $temStock = Schema::hasColumn('livros', 'stock');

        // cleanup caso exista de tentativa anterior
        DB::statement('DROP TABLE IF EXISTS livros_temp');

        Schema::create('livros_temp', function ($table) {
            $table->id();
            $table->string('isbn');
            $table->string('nome');
            $table->unsignedBigInteger('editora_id')->nullable();
            $table->text('bibliografia')->nullable();
            $table->string('imagem_capa')->nullable();
            $table->decimal('preco', 10, 2)->nullable();
            $table->unsignedInteger('stock')->default(1);
            $table->timestamps();
        });

        $selectNome = $colNomeAtual ? $colNomeAtual : "''";
        $selectStock = $temStock ? 'stock' : '1';

        DB::statement("
            INSERT INTO livros_temp (
                id, isbn, nome, editora_id, bibliografia, imagem_capa, preco, stock, created_at, updated_at
            )
            SELECT
                id,
                isbn,
                $selectNome as nome,
                editora_id,
                bibliografia,
                imagem_capa,
                preco,
                $selectStock as stock,
                created_at,
                updated_at
            FROM livros
        ");

        Schema::drop('livros');
        Schema::rename('livros_temp', 'livros');
    }

    public function down(): void
    {
        // Se não for sqlite, reverter para NOT NULL (se fizer sentido no teu projeto)
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('livros', function ($table) {
                $table->decimal('preco', 10, 2)->nullable(false)->change();
            });
            return;
        }

        // SQLITE rollback: voltar preco NOT NULL
        DB::statement('DROP TABLE IF EXISTS livros_temp');

        Schema::create('livros_temp', function ($table) {
            $table->id();
            $table->string('isbn');
            $table->string('nome');
            $table->unsignedBigInteger('editora_id')->nullable();
            $table->text('bibliografia')->nullable();
            $table->string('imagem_capa')->nullable();
            $table->decimal('preco', 10, 2); // NOT NULL
            $table->unsignedInteger('stock')->default(1);
            $table->timestamps();
        });

        // se preco era null, volta para 0.00 no rollback
        DB::statement("
            INSERT INTO livros_temp (
                id, isbn, nome, editora_id, bibliografia, imagem_capa, preco, stock, created_at, updated_at
            )
            SELECT
                id,
                isbn,
                nome,
                editora_id,
                bibliografia,
                imagem_capa,
                COALESCE(preco, 0) as preco,
                COALESCE(stock, 1) as stock,
                created_at,
                updated_at
            FROM livros
        ");

        Schema::drop('livros');
        Schema::rename('livros_temp', 'livros');
    }
};

