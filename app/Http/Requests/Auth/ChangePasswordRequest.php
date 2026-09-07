<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\ChangePasswordDTO;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ];
    }

    public function toDTO(): ChangePasswordDTO
    {
        return ChangePasswordDTO::fromArray($this->validated());
    }
}
