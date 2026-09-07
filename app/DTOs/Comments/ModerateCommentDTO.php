<?php

namespace App\DTOs\Comments;

readonly class ModerateCommentDTO
{
    public function __construct(
        public string $status,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
        );
    }
}
