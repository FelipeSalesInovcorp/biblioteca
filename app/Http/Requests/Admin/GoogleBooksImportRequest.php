<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GoogleBooksImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'google_volume_id' => ['required', 'string', 'max:100'],
        ];
    }

    public function googleVolumeId(): string
    {
        return (string) $this->validated()['google_volume_id'];
    }
}
