<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cleanup caso uma execução anterior tenha falhado a meio
        DB::statement('DROP TABLE IF EXISTS livros_temp');
        DB::statement('DROP INDEX IF EXISTS livros_temp_isbn_unique');
        DB::statement('DROP INDEX IF EXISTS livros_isbn_unique'); // por precaução

        // Criar tabela temporária com preco NULL
        DB::statement('
            CREATE TABLE livros_temp (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                isbn VARCHAR NOT NULL,
                nome VARCHAR NOT NULL,
                editora_id INTEGER NULL,
                bibliografia TEXT NULL,
                imagem_capa VARCHAR NULL,
                preco DECIMAL(10,2) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                FOREIGN KEY (editora_id) REFERENCES editoras(id) ON DELETE CASCADE
            )
        ');

        // Copiar dados
        DB::statement('
            INSERT INTO livros_temp (
                id, isbn, nome, editora_id, bibliografia, imagem_capa, preco, created_at, updated_at
            )
            SELECT
                id, isbn, nome, editora_id, bibliografia, imagem_capa, preco, created_at, updated_at
            FROM livros
        ');

        // UNIQUE no ISBN (no temp com nome diferente)
        DB::statement('CREATE UNIQUE INDEX livros_temp_isbn_unique ON livros_temp (isbn)');

        // Substituir tabela
        Schema::drop('livros');
        Schema::rename('livros_temp', 'livros');

        // Recriar índice "oficial"
        DB::statement('DROP INDEX IF EXISTS livros_isbn_unique');
        DB::statement('CREATE UNIQUE INDEX livros_isbn_unique ON livros (isbn)');
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
