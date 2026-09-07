<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class SyncPostViews extends Command
{
    protected $signature = 'posts:sync-views';

    protected $description = 'مزامنة مشاهدات المقالات من Redis إلى قاعدة البيانات دفعة واحدة';

    public function handle(): int
    {
        $views = Redis::hgetall('posts_views_buffer');

        if (empty($views)) {
            return Command::SUCCESS;
        }

        DB::transaction(function () use ($views) {
            foreach ($views as $postId => $count) {
                Post::where('id', (int) $postId)->increment('views_count', (int) $count);
            }
            Redis::del('posts_views_buffer');
        });

        $this->info('تمت مزامنة المشاهدات بنجاح.');

        return Command::SUCCESS;
    }
}
