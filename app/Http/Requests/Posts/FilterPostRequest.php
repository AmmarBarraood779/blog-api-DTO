<?php

namespace App\Http\Requests\Posts;

use App\DTOs\Posts\PostFilterDTO;
use Illuminate\Foundation\Http\FormRequest;

class FilterPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function toDTO(): PostFilterDTO
    {
        return PostFilterDTO::fromArray($this->validated());
    }
}
