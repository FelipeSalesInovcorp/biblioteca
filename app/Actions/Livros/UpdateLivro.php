<?php

namespace App\Actions\Livros;

use App\Actions\Uploads\StorePublicImage;
use App\Models\Livro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateLivro
{
    public function __construct(private StorePublicImage $storeImage) {}

    public function execute(Livro $livro, array $data): Livro
    {
        return DB::transaction(function () use ($livro, $data) {
            $autores = $data['autores'] ?? [];
            unset($data['autores']);

            if (!empty($data['imagem_capa']) && $data['imagem_capa'] instanceof \Illuminate\Http\UploadedFile) {
                if ($livro->imagem_capa) {
                    Storage::disk('public')->delete($livro->imagem_capa);
                }
                $data['imagem_capa'] = $this->storeImage->execute($data['imagem_capa'], 'livros');
            }

            $livro->update($data);
            $livro->autores()->sync($autores);

            return $livro;
        });
    }
}
