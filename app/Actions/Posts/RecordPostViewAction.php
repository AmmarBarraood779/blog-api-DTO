<?php

namespace App\Actions\Posts;

use App\Models\Post;
use Illuminate\Support\Facades\Redis;

class RecordPostViewAction
{
    public function execute(Post $post, string $ip): bool
    {
        $throttleKey = "viewed:post:{$post->id}:{$ip}";

        if (! Redis::exists($throttleKey)) {
            Redis::setex($throttleKey, 7200, 1);
            Redis::hincrby('posts_views_buffer', (string) $post->id, 1);

            return true;
        }

        return false;
    }
}
