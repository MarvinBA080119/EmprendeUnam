<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RestablecerClaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'correo' => ['required', 'string', 'email', 'max:254'],
            'clave' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
        ];
    }
}
