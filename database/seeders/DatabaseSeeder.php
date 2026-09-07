<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $admin = User::factory()->admin()->create([
            'name' => 'Ammar Admin',
            'email' => 'admin@blog.com',
            'password' => Hash::make('password123'),
        ]);

        $defaultUser = User::factory()->create([
            'name' => 'Standard User',
            'email' => 'user@blog.com',
            'password' => Hash::make('password123'),
        ]);

        $users = User::factory(8)->create();
        $allUsers = $users->concat([$admin, $defaultUser]);

        $categories = Category::factory(6)->create();
        $tags = Tag::factory(12)->create();

        $posts = Post::factory(25)->recycle($categories)->recycle($allUsers)->create()->each(function (Post $post) use ($tags) {

            $post->tags()->attach(
                $tags->random(rand(1, 4))->pluck('id')->toArray()
            );
        });

        Comment::factory(60)->recycle($posts)->recycle($allUsers)->create();
    }
}
