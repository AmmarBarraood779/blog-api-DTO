<?php

namespace App\Http\Requests\Categories;

use App\DTOs\Tags\TagDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends FormRequest
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
                Rule::unique('tags', 'name')->ignore($this->route('tag')),
            ],
        ];
    }

    public function toDTO(): TagDTO
    {
        return TagDTO::fromArray($this->validated());
    }
}
