<?php

namespace App\Actions\Uploads;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class StorePublicImage
{
    public function execute(UploadedFile $file, string $folder): string
    {
        $name = Str::uuid().'.'.$file->getClientOriginalExtension();
        return $file->storeAs($folder, $name, 'public');
    }
}
