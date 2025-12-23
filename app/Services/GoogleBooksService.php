<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleBooksService
{
    private string $baseUrl;
    private ?string $apiKey;
    private int $maxResults;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.google_books.base_url'), '/');
        $this->apiKey = config('services.google_books.api_key');
        $this->maxResults = (int) config('services.google_books.max_results', 10);
    }

    /**
     * Pesquisa volumes na Google Books API.
     * Ex: $service->search('asimov', 10);
     */
    /*public function search(string $query, ?int $maxResults = null, int $startIndex = 0): array
    {
        $query = trim($query);
        if ($query === '') {
            return [
                'totalItems' => 0,
                'items' => [],
            ];
        }

        $max = $maxResults ?? $this->maxResults;

        $response = Http::timeout(15)
            ->retry(2, 200)
            ->get($this->baseUrl . '/volumes', array_filter([
                'q' => $query,
                'maxResults' => $max,
                'startIndex' => $startIndex,
                'key' => $this->apiKey, // pode ser null (a API funciona sem key em alguns cenários)
            ]));

        if (! $response->successful()) {
            throw new RuntimeException(
                'Erro ao consultar Google Books API: ' . $response->status() . ' - ' . $response->body()
            );
        }

        $data = $response->json();

        return [
            'totalItems' => $data['totalItems'] ?? 0,
            'items' => array_map([$this, 'mapVolumeToLocalPreview'], $data['items'] ?? []),
        ];
    }*/

    public function search(string $query, ?int $maxResults = null, int $startIndex = 0): array
    {
        $query = trim($query);
        if ($query === '') {
            return [
                'totalItems' => 0,
                'items' => [],
            ];
        }

        $max = $maxResults ?? $this->maxResults;

        $response = Http::timeout(15)
            ->retry(2, 200)
            ->withOptions([
                'verify' => false, // ← ADICIONE ESTA LINHA
            ])
            ->get($this->baseUrl . '/volumes', array_filter([
                'q' => $query,
                'maxResults' => $max,
                'startIndex' => $startIndex,
                'key' => $this->apiKey,
            ]));

        if (! $response->successful()) {
            throw new RuntimeException(
                'Erro ao consultar Google Books API: ' . $response->status() . ' - ' . $response->body()
            );
        }

        $data = $response->json();

        return [
            'totalItems' => $data['totalItems'] ?? 0,
            'items' => array_map([$this, 'mapVolumeToLocalPreview'], $data['items'] ?? []),
        ];
    }



    /**
     * Obtém 1 volume específico pelo ID.
     */
    public function getVolume(string $googleVolumeId): array
    {
        $googleVolumeId = trim($googleVolumeId);
        if ($googleVolumeId === '') {
            throw new RuntimeException('googleVolumeId inválido.');
        }

        $response = Http::timeout(15)
            ->retry(2, 200)
            ->get($this->baseUrl . '/volumes/' . $googleVolumeId, array_filter([
                'key' => $this->apiKey,
            ]));

        if (! $response->successful()) {
            throw new RuntimeException(
                'Erro ao obter volume Google Books: ' . $response->status() . ' - ' . $response->body()
            );
        }

        return $this->mapVolumeToLocalPreview($response->json());
    }

    /**
     * Mapeia um volume (Google) para um preview compatível com o teu BD (sem gravar).
     * Ajustável aos teus campos: Livro(nome,isbn,preco,bibliografia,imagem_capa), Editora(nome), Autores(nome)
     */
    private function mapVolumeToLocalPreview(array $volume): array
    {
        $info = $volume['volumeInfo'] ?? [];
        $sale = $volume['saleInfo'] ?? [];
        $images = $info['imageLinks'] ?? [];

        $isbn = $this->extractIsbn13($info['industryIdentifiers'] ?? [])
            ?? $this->extractIsbn10($info['industryIdentifiers'] ?? []);

        $title = $info['title'] ?? 'Sem título';
        $subtitle = $info['subtitle'] ?? null;

        // “bibliografia” no teu projeto parece ser descrição/resumo
        $description = $info['description'] ?? null;

        // Editor(a) -> Editora
        $publisher = $info['publisher'] ?? null;

        // Autores
        $authors = $info['authors'] ?? [];

        // Preço (pode não existir)
        $price = null;
        if (($sale['saleability'] ?? null) === 'FOR_SALE') {
            $listPrice = $sale['listPrice']['amount'] ?? null;
            $price = is_numeric($listPrice) ? (float) $listPrice : null;
        }

        // Capa (URL). Para gravar localmente vais descarregar depois na Action.
        $thumbnail = $images['thumbnail'] ?? $images['smallThumbnail'] ?? null;
        $thumbnail = $this->normalizeCoverUrl($thumbnail);

        return [
            'google_volume_id' => $volume['id'] ?? null,

            // Campos “Livro”
            'nome' => $subtitle ? ($title . ': ' . $subtitle) : $title,
            'isbn' => $isbn,
            'bibliografia' => $description,
            'preco' => $price,

            // Relações
            'editora_nome' => $publisher,
            'autores' => array_values(array_filter($authors)),

            // Auxiliares
            'capa_url' => $thumbnail,
            'published_date' => $info['publishedDate'] ?? null,
            'page_count' => $info['pageCount'] ?? null,
            'language' => $info['language'] ?? null,
        ];
    }

    private function extractIsbn13(array $identifiers): ?string
    {
        foreach ($identifiers as $id) {
            if (($id['type'] ?? null) === 'ISBN_13' && !empty($id['identifier'])) {
                return preg_replace('/\D+/', '', $id['identifier']);
            }
        }
        return null;
    }

    private function extractIsbn10(array $identifiers): ?string
    {
        foreach ($identifiers as $id) {
            if (($id['type'] ?? null) === 'ISBN_10' && !empty($id['identifier'])) {
                return preg_replace('/\D+/', '', $id['identifier']);
            }
        }
        return null;
    }

    /**
     * Normaliza a URL da capa para https e tenta “forçar” melhor qualidade quando possível.
     */
    private function normalizeCoverUrl(?string $url): ?string
    {
        if (! $url) return null;

        $url = str_replace('http://', 'https://', $url);

        // A Google muitas vezes vem com parâmetros tipo &zoom=1
        // Podemos tentar um zoom maior (nem sempre funciona, mas ajuda)
        if (str_contains($url, 'zoom=')) {
            $url = preg_replace('/zoom=\d+/', 'zoom=2', $url);
        }

        return $url;
    }
}
