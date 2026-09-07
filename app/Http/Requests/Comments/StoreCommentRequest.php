<?php

namespace App\Http\Requests\Comments;

use App\DTOs\Comments\CreateCommentDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:3', 'max:1000'],
        ];
    }

    public function toDTO(int $postId): CreateCommentDTO
    {
        return CreateCommentDTO::fromArray($this->validated(), $postId);
    }
}
