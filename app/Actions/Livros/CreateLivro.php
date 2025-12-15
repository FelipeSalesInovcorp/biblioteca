<?php

namespace App\Actions\Livros;

use App\Actions\Uploads\StorePublicImage;
use App\Models\Livro;
use Illuminate\Support\Facades\DB;

class CreateLivro
{
    public function __construct(private StorePublicImage $storeImage) {}

    public function execute(array $data): Livro
    {
        return DB::transaction(function () use ($data) {
            $autores = $data['autores'] ?? [];
            unset($data['autores']);

            if (!empty($data['imagem_capa']) && $data['imagem_capa'] instanceof \Illuminate\Http\UploadedFile) {
                $data['imagem_capa'] = $this->storeImage->execute($data['imagem_capa'], 'livros');
            }

            $livro = Livro::create($data);
            $livro->autores()->sync($autores);

            return $livro;
        });
    }
}
