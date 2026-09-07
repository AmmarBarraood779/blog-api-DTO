<?php

namespace App\Http\Requests\Categories;

use App\DTOs\Categories\CategoryDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ];
    }

    public function toDTO(): CategoryDTO
    {
        return CategoryDTO::fromArray($this->validated());
    }
}
