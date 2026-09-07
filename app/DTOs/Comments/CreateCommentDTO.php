<?php

namespace App\DTOs\Comments;

readonly class CreateCommentDTO
{
    public function __construct(
        public int $postId,
        public string $body,
    ) {}

    public static function fromArray(array $data, int $postId): self
    {
        return new self(
            postId: $postId,
            body: $data['body'],
        );
    }
}
