<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
        return true; // Authorization is handled in the controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Validation rules for creating a chat room

            'name' => ['required', 'string', 'max:120'],
            'reference' => ['nullable', 'string', 'max:100', 'alpha_dash', 'unique:rooms,reference'],
            'avatar' => ['nullable', 'file', 'image', 'max:2048'],
        ];
    }
}
