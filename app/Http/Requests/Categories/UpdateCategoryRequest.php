<?php

namespace App\Http\Requests\Categories;

use App\DTOs\Categories\CategoryDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($this->route('category')),
            ],
        ];
    }

    public function toDTO(): CategoryDTO
    {
        return CategoryDTO::fromArray($this->validated());
    }
}
