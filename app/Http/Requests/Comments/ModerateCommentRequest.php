<?php

namespace App\Http\Requests\Comments;

use App\DTOs\Comments\ModerateCommentDTO;
use Illuminate\Foundation\Http\FormRequest;

class ModerateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:approved,rejected,pending'],
        ];
    }

    public function toDTO(): ModerateCommentDTO
    {
        return ModerateCommentDTO::fromArray($this->validated());
    }
}
