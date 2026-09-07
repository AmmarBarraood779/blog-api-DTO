<?php

namespace App\DTOs\Posts;

use Illuminate\Http\UploadedFile;

readonly class CreatePostDTO
{
    public function __construct(
        public int $categoryId,
        public string $title,
        public string $body,
        public string $status,
        public ?UploadedFile $featuredImage,
        public array $tags = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: (int) $data['category_id'],
            title: $data['title'],
            body: $data['body'],
            status: $data['status'] ?? 'draft',
            featuredImage: $data['featured_image'] ?? null,
            tags: $data['tags'] ?? [],
        );
    }
}
