<?php

namespace App\Http\Requests\Tags;

use App\DTOs\Tags\TagDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50', 'unique:tags,name'],
        ];
    }

    public function toDTO(): TagDTO
    {
        return TagDTO::fromArray($this->validated());
    }
}
