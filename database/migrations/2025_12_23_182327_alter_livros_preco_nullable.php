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
            Schema::table('livros', function (Blueprint $table) {
                $table->decimal('preco', 10, 2)->nullable()->change();
            });
            return;
        }

        // SQLITE: recriar a tabela (workaround)
        $temNome = Schema::hasColumn('livros', 'nome');
        $temTitulo = Schema::hasColumn('livros', 'titulo');

        // coluna de nome existente na tabela atual (antes da migração)
        $colNomeAtual = $temNome ? 'nome' : ($temTitulo ? 'titulo' : null);

        // 1) criar tabela temporária com a estrutura final (inclui nome)
        Schema::create('livros_temp', function (Blueprint $table) {
            $table->id();
            $table->string('isbn');
            $table->string('nome'); // estrutura final desejada
            $table->unsignedBigInteger('editora_id')->nullable();
            $table->text('bibliografia')->nullable();
            $table->string('imagem_capa')->nullable();
            $table->decimal('preco', 10, 2)->nullable(); // aqui é o objetivo: nullable
            $table->unsignedInteger('stock')->default(1);
            $table->timestamps();
        });

        // 2) copiar dados do livros -> livros_temp
        // Se antes a coluna chamava "titulo", copiamos como "nome"
        $selectNome = $colNomeAtual ? $colNomeAtual : "''";

        // stock pode não existir ainda nesta fase, então metemos 1 quando não existir
        $temStock = Schema::hasColumn('livros', 'stock');
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

        // 3) apagar tabela antiga e renomear temp
        Schema::drop('livros');
        Schema::rename('livros_temp', 'livros');
    }


    public function down(): void
    {
        // Cleanup
        DB::statement('DROP TABLE IF EXISTS livros_temp');
        DB::statement('DROP INDEX IF EXISTS livros_temp_isbn_unique');
        DB::statement('DROP INDEX IF EXISTS livros_isbn_unique');

        // Voltar preco NOT NULL
        DB::statement('
            CREATE TABLE livros_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                isbn VARCHAR NOT NULL,
                nome VARCHAR NOT NULL,
                editora_id INTEGER NULL,
                bibliografia TEXT NULL,
                imagem_capa VARCHAR NULL,
                preco DECIMAL(10,2) NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (editora_id) REFERENCES editoras(id) ON DELETE CASCADE
            )
        ');

        DB::statement('
            INSERT INTO livros_temp (
                id, isbn, nome, editora_id, bibliografia, imagem_capa, preco, created_at, updated_at
            )
            SELECT
                id, isbn, nome, editora_id, bibliografia, imagem_capa, IFNULL(preco, 0), created_at, updated_at
            FROM livros
        ');

        DB::statement('CREATE UNIQUE INDEX livros_temp_isbn_unique ON livros_temp (isbn)');

        Schema::drop('livros');
        Schema::rename('livros_temp', 'livros');

        // Recriar índice "oficial"
        DB::statement('DROP INDEX IF EXISTS livros_isbn_unique');
        DB::statement('CREATE UNIQUE INDEX livros_isbn_unique ON livros (isbn)');
    }
};
