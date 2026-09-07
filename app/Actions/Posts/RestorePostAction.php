<?php

namespace App\Actions\Posts;

use App\Models\Post;

class RestorePostAction
{
    public function execute(Post $post): Post
    {
        $post->restore();

        return $post;
    }
}
