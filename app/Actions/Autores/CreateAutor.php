<?php

namespace App\Actions\Autores;

use App\Actions\Uploads\StorePublicImage;
use App\Models\Autor;

class CreateAutor
{
    public function __construct(private StorePublicImage $storeImage) {}

    public function execute(array $data): Autor
    {
        if (!empty($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
            $data['foto'] = $this->storeImage->execute($data['foto'], 'autores');
        }

        return Autor::create($data);
    }
}
