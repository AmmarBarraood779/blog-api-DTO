<?php

namespace App\Actions\Posts;

use App\DTOs\Posts\CreatePostDTO;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class CreatePostAction
{
    public function execute(User $user, CreatePostDTO $dto): Post
    {
        $imagePath = null;
        if ($dto->featuredImage) {
            $imagePath = $dto->featuredImage->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => $user->id,
            'category_id' => $dto->categoryId,
            'title' => $dto->title,
            'slug' => Str::slug($dto->title).'-'.Str::random(6),
            'body' => $dto->body,
            'featured_image' => $imagePath,
            'status' => $dto->status,
        ]);

        if (! empty($dto->tags)) {
            $post->tags()->sync($dto->tags);
        }

        return $post->load(['user', 'category', 'tags']);
    }
}
