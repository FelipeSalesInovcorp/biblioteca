<?php

namespace App\Actions\Autores;

use App\Actions\Uploads\StorePublicImage;
use App\Models\Autor;
use Illuminate\Support\Facades\Storage;

class UpdateAutor
{
    public function __construct(private StorePublicImage $storeImage) {}

    public function execute(Autor $autor, array $data): Autor
    {
        if (!empty($data['foto']) && $data['foto'] instanceof \Illuminate\Http\UploadedFile) {
            if ($autor->foto) {
                Storage::disk('public')->delete($autor->foto);
            }

            $data['foto'] = $this->storeImage->execute($data['foto'], 'autores');
        }

        $autor->update($data);

        return $autor;
    }
}
