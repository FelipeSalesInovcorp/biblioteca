<?php

namespace App\Actions\Editoras;

use App\Actions\Uploads\StorePublicImage;
use App\Models\Editora;

class CreateEditora
{
    public function __construct(private StorePublicImage $storeImage) {}

    public function execute(array $data): Editora
    {
        if (!empty($data['logotipo']) && $data['logotipo'] instanceof \Illuminate\Http\UploadedFile) {
            $data['logotipo'] = $this->storeImage->execute($data['logotipo'], 'editoras');
        }

        return Editora::create($data);
    }
}
