<?php

namespace App\Http\Requests\Auth;

use App\DTOs\Auth\RegisterDTO;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_admin' => [
                'nullable',
                'boolean',
                function (string $attribute, mixed $value, \Closure $fail) {

                    if ($value && ! $this->user('sanctum')?->is_admin) {
                        $fail('عذراً، المشرف فقط هو من يملك صلاحية إنشاء حساب مشرف آخر.');
                    }
                },
            ],
        ];

    }

    public function toDTO(): RegisterDTO
    {
        return RegisterDTO::fromArray($this->validated());
    }
}
