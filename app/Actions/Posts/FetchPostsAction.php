<?php

namespace App\Actions\Posts;

use App\DTOs\Posts\PostFilterDTO;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FetchPostsAction
{
    public function execute(PostFilterDTO $dto, ?User $user = null): LengthAwarePaginator
    {
        return Post::with(['user', 'category', 'tags'])
            ->when(! ($user && $user->is_admin), function ($query) {
                $query->published();
            })
            ->filter($dto->search, $dto->category)
            ->latest()
            ->paginate($dto->perPage);
    }
}
