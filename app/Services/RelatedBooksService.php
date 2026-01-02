<?php

namespace App\Services;

use App\Models\Livro;
use Illuminate\Support\Str;

class RelatedBooksService
{

    //Retorna uma coleção de livros relacionados (ordenados por score desc).
    //public function getRelated(Livro $livro, int $limit = 6)
    public function getRelated(Livro $livro, int $limit = 6, float $minScore = 0.04) 
    {
        $baseText = (string) ($livro->bibliografia ?? '');
        $baseTokens = $this->tokens($baseText);

        // Se não tiver bibliografia suficiente, tenta uma heurística simples:
        if (count($baseTokens) < 6) {
            return $this->fallbackRelated($livro, $limit);
        }

        // Buscar candidatos por relações “baratas” (autores/editora)
        $candidates = $this->candidateBooks($livro);

        // Se não houver candidatos suficientes, amplia para livros com bibliografia preenchida
        if ($candidates->count() < 6) {
            $extra = Livro::query()
                ->whereKeyNot($livro->id)
                ->whereNotNull('bibliografia')
                ->with(['editora', 'autores'])
                ->orderByDesc('id')
                ->limit(150)
                ->get();

            $candidates = $candidates->merge($extra)
                ->unique('id')
                ->values();
        }


        // Calcular score por similaridade de palavras (Jaccard)
        $scored = $candidates->map(function ($cand) use ($baseTokens) {
            $candTokens = $this->tokens((string) ($cand->bibliografia ?? ''));
            $score = $this->jaccard($baseTokens, $candTokens);

            // (não inventa relação manual, só melhora ranking)
            return [
                'book' => $cand,
                'score' => $score,
            ];
        });

        // Filtrar scores baixos e devolver top N
        return $scored
            ->filter(fn ($x) => $x['score'] > $minScore)     // podes subir para 0.05 se quiseres mais “inteligente”
            ->sortByDesc('score')
            ->take($limit)
            ->pluck('book')
            ->values();
            
    }

    private function candidateBooks(Livro $livro)
    {
        $authorIds = $livro->autores->pluck('id')->all();
        $editoraId = $livro->editora_id;

        return Livro::query()
            ->whereKeyNot($livro->id)
            ->whereNotNull('bibliografia') // só livros com bibliografia; Alterei aqui
            ->where(function ($q) use ($editoraId, $authorIds) {
                // mesma editora

                if ($editoraId) {
                    $q->where('editora_id', $editoraId);
                }

                // ou pelo menos 1 autor em comum
                if (!empty($authorIds)) {
                    $q->orWhereHas('autores', fn($qa) => $qa->whereIn('autores.id', $authorIds));
                }
            })
            ->with(['editora', 'autores'])
            ->limit(50)
            ->get()
            // redundância segura: garante que nunca vem o próprio livro
            ->reject(fn($b) => (int)$b->id === (int)$livro->id)
            ->values();
    }


    private function fallbackRelated(Livro $livro, int $limit)
    {
        // fallback: mesma editora (ou últimos livros) se não houver descrição suficiente
        return Livro::query()
            ->whereKeyNot($livro->id)
            ->with(['editora', 'autores'])
            ->when($livro->editora_id, fn ($q) => $q->where('editora_id', $livro->editora_id))
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    
     //Tokenização: normaliza, remove pontuação, stopwords, palavras curtas.
    private function tokens(string $text): array
    {
        $text = Str::lower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text); // remove pontuação
        $parts = preg_split('/\s+/u', trim($text));

        $stop = $this->stopwordsPt();

        $tokens = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') continue;
            if (mb_strlen($p) < 4) continue;     // evita “de”, “um”, “a”, etc.
            if (isset($stop[$p])) continue;

            $tokens[] = $p;
        }

        // valores únicos ajudam o Jaccard
        return array_values(array_unique($tokens));
    }

    //Similaridade Jaccard = |A ∩ B| / |A ∪ B|
    private function jaccard(array $a, array $b): float
    {
        if (empty($a) || empty($b)) return 0.0;

        $setA = array_fill_keys($a, true);
        $setB = array_fill_keys($b, true);

        $inter = array_intersect_key($setA, $setB);
        $union = $setA + $setB;

        return count($inter) / max(1, count($union));
    }

    private function stopwordsPt(): array
    {
        static $stop = null;
        if ($stop !== null) return $stop;

        $words = [
            'para','com','sem','uma','umas','uns','por','que','como','sobre','entre','tambem','também',
            'mais','menos','muito','muita','muitos','muitas','este','esta','isto','esse','essa','isso',
            'aquele','aquela','aquilo','quando','onde','porque','ser','estar','ter','tinha','tiver','foi',
            'sao','são','dos','das','nos','nas','aos','às','de','do','da','em','no','na','os','as','um','uma',
            'e','ou','ao','à','se','nao','não','lhe','eles','elas','dele','dela','seu','sua','seus','suas','isso','meu','minha','meus','minhas','sempre',
            'já','ja','assim','só','so','até','ate','também','tambem','nosso','nossa','nossos','nossas','você','voce','vocês','voces',
        ];

        $stop = [];
        foreach ($words as $w) $stop[$w] = true;
        return $stop;
    }
}
