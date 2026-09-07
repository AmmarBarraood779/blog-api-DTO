<?php

namespace App\DTOs\Posts;

use Illuminate\Http\UploadedFile;

readonly class UpdatePostDTO
{
    public function __construct(
        public ?int $categoryId,
        public ?string $title,
        public ?string $body,
        public ?string $status,
        public ?UploadedFile $featuredImage,
        public ?array $tags,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            categoryId: isset($data['category_id']) ? (int) $data['category_id'] : null,
            title: $data['title'] ?? null,
            body: $data['body'] ?? null,
            status: $data['status'] ?? null,
            featuredImage: $data['featured_image'] ?? null,
            tags: $data['tags'] ?? null,
        );
    }
}
