<?php

namespace App\Actions\Posts;

use App\Models\Post;

class DeletePostAction
{
    public function execute(Post $post): void
    {
        $post->delete();
    }
}
