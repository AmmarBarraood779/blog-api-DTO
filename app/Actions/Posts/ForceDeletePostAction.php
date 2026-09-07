<?php

namespace App\Actions\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class ForceDeletePostAction
{
    public function execute(Post $post): void
    {

        if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->forceDelete();
    }
}
