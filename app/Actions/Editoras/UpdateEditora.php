<?php

namespace App\Actions\Editoras;

use App\Actions\Uploads\StorePublicImage;
use App\Models\Editora;
use Illuminate\Support\Facades\Storage;

class UpdateEditora
{
    public function __construct(private StorePublicImage $storeImage) {}

    public function execute(Editora $editora, array $data): Editora
    {
        if (!empty($data['logotipo']) && $data['logotipo'] instanceof \Illuminate\Http\UploadedFile) {
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }

            $data['logotipo'] = $this->storeImage->execute($data['logotipo'], 'editoras');
        }

        $editora->update($data);

        return $editora;
    }
}
