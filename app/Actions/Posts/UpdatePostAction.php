<?php

namespace App\Actions\Posts;

use App\DTOs\Posts\UpdatePostDTO;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdatePostAction
{
    public function execute(Post $post, UpdatePostDTO $dto): Post
    {
        $data = [];

        if ($dto->title !== null) {
            $data['title'] = $dto->title;
            $data['slug'] = Str::slug($dto->title).'-'.Str::random(6);
        }

        if ($dto->body !== null) {
            $data['body'] = $dto->body;
        }

        if ($dto->categoryId !== null) {
            $data['category_id'] = $dto->categoryId;
        }

        if ($dto->status !== null) {
            $data['status'] = $dto->status;
        }

        if ($dto->featuredImage) {

            if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $data['featured_image'] = $dto->featuredImage->store('posts', 'public');
        }

        $post->update($data);

        if ($dto->tags !== null) {
            $post->tags()->sync($dto->tags);
        }

        return $post->load(['user', 'category', 'tags']);
    }
}
