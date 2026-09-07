<?php

namespace Tests\Feature\Api;

use App\Jobs\ModerateCommentJob;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $category = Category::factory()->create();
        $this->post = Post::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_user_can_submit_comment_and_dispatch_moderation_job(): void
    {
        Queue::fake();
        Sanctum::actingAs($this->user);

        $payload = ['body' => 'هذا تعليق تجريبي لاختبار مسار العمل بالكامل.'];

        $response = $this->postJson("/api/posts/{$this->post->id}/comments", $payload);

        $response->assertStatus(201)
            ->assertJsonPath('comment.status', 'pending')
            ->assertJsonPath('comment.body', $payload['body']);

        $this->assertDatabaseHas('comments', [
            'post_id' => $this->post->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
            'body' => $payload['body'],
        ]);

        Queue::assertPushedOn('moderation', ModerateCommentJob::class);
    }

    public function test_comment_submission_fails_with_invalid_body(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/posts/{$this->post->id}/comments", [
            'body' => 'ab',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['body']);
    }
}
