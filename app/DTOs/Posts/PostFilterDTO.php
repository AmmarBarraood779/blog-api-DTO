<?php

namespace App\DTOs\Posts;

readonly class PostFilterDTO
{
    public function __construct(
        public ?string $search,
        public ?string $category,
        public int $perPage,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            search: $data['search'] ?? null,
            category: $data['category'] ?? null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 10,
        );
    }
}
