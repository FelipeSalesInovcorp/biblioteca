<?php

namespace App\Services\Exports;

use App\Models\Livro;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LivrosCsvExporter
{
    public function stream(): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="livros.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // BOM para Excel reconhecer UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabeçalhos
            fputcsv($handle, ['ISBN', 'Nome', 'Editora', 'Autores', 'Preço'], ';');

            Livro::with(['editora', 'autores'])
                ->orderBy('nome')
                ->chunk(200, function ($livros) use ($handle) {
                    foreach ($livros as $livro) {
                        $autores = $livro->autores->pluck('nome')->join(', ');

                        fputcsv($handle, [
                            $livro->isbn,
                            $livro->nome,
                            optional($livro->editora)->nome,
                            $autores,
                            $livro->preco_formatado_sem_simbolo,
                        ], ';');
                    }
                });

            fclose($handle);
        }, 200, $headers);
    }
}
