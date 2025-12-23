<?php

namespace App\Actions\Livros;

use App\Models\Autor;
use App\Models\Editora;
use App\Models\Livro;
use App\Services\GoogleBooksService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ImportLivroFromGoogleBooks
{
    public function __construct(
        private readonly GoogleBooksService $googleBooks
    ) {}

    public function handle(string $googleVolumeId): Livro
    {
        // 1) Obter dados completos do volume (preview mapeado)
        $data = $this->googleBooks->getVolume($googleVolumeId);

        // 2) Regras mínimas de integridade (ajusta conforme o teu BD)
        $isbn = $data['isbn'] ?? null;
        if (!$isbn) {
            throw new RuntimeException('Não foi possível importar: ISBN em falta.');
        }

        // Anti-duplicação: se ISBN já existir, devolve o existente
        $existing = Livro::where('isbn', $isbn)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($data, $isbn) {

            // 3) Editora (cria se não existir)
            $editora = null;
            if (!empty($data['editora_nome'])) {
                $editora = Editora::firstOrCreate(
                    ['nome' => $data['editora_nome']],
                    [
                        'logotipo' => 'img/editora-default.png', // ou null se for nullable
                        'notas' => 'Criada automaticamente via importação Google Books.',
                    ]
                );
            }


            // 4) Capa: descarregar (opcional), guardar em storage e gravar path
            $capaPath = $this->downloadCoverIfAny($data['capa_url'] ?? null, $isbn);

            // 5) Criar Livro
            $livro = Livro::create([
                'isbn' => $isbn,
                'nome' => $data['nome'] ?? 'Sem título',
                'bibliografia' => $data['bibliografia'] ?? null,
                'preco' => $data['preco'] ?? null,
                'imagem_capa' => $capaPath,
                'editora_id' => $editora?->id,
            ]);

            // 6) Autores (criar se não existir) + attach
            $autores = (array) ($data['autores'] ?? []);
            $autorIds = [];

            foreach ($autores as $nomeAutor) {
                $nomeAutor = trim((string) $nomeAutor);
                if ($nomeAutor === '') continue;

                $autor = Autor::firstOrCreate(
                    ['nome' => $nomeAutor],
                    [ // Valores para criar novo registro
                        'foto' => 'img/autor-default.png', // CAMPO OBRIGATÓRIO
                        'notas' => 'Criado automaticamente via importação Google Books.',
                    ]
                );
                $autorIds[] = $autor->id;
            }

            if (!empty($autorIds)) {
                $livro->autores()->sync($autorIds);
            }

            return $livro;
        });
    }

    private function downloadCoverIfAny(?string $url, string $isbn): ?string
    {
        if (!$url) return null;

        // Nome previsível
        $filename = 'capas/' . Str::slug($isbn) . '.jpg';

        try {
            $resp = Http::timeout(20)->get($url);
            if (!$resp->successful()) return null;

            Storage::disk('public')->put($filename, $resp->body());

            // grava o path relativo do disk public
            return $filename;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}
