<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompleteBlogApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $user1;

    protected User $user2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();
    }

    // ==========================================
    // 1. فحص Module 1: User & Authentication
    // ==========================================

    public function test_user_can_register_successfully(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ali Ahmed',
            'email' => 'ali@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'is_admin']]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user1->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_update_profile_and_change_password(): void
    {
        // 1. تحديث الملف الشخصي
        $updateResponse = $this->actingAs($this->user1, 'sanctum')->putJson('/api/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('user.name', 'Updated Name')
            ->assertJsonPath('user.email', 'updated@example.com');

        // 2. تغيير كلمة المرور بكلمة حالية خاطئة
        $this->actingAs($this->user1, 'sanctum')->putJson('/api/profile/change-password', [
            'current_password' => 'wrong-current',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ])->assertStatus(422);

        // 3. تغيير كلمة المرور بنجاح
        $this->actingAs($this->user1, 'sanctum')->putJson('/api/profile/change-password', [
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ])->assertStatus(200);
    }

    public function test_user_can_logout_and_revoke_token(): void
    {
        $token = $this->user1->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    // ==========================================
    // 2. فحص Module 3: Categories & Tags (RBAC)
    // ==========================================

    public function test_regular_user_cannot_create_or_delete_categories(): void
    {
        $category = Category::factory()->create();

        // المستخدم العادي لا يستطيع إنشاء تصنيف (403)
        $this->actingAs($this->user1, 'sanctum')->postJson('/api/categories', [
            'name' => 'Tech',
        ])->assertStatus(403);

        // المستخدم العادي لا يستطيع حذف تصنيف (403)
        $this->actingAs($this->user1, 'sanctum')->deleteJson("/api/categories/{$category->id}")
            ->assertStatus(403);
    }

    public function test_admin_can_manage_categories_and_tags(): void
    {
        // المشرف ينشئ تصنيفاً
        $createCat = $this->actingAs($this->admin, 'sanctum')->postJson('/api/categories', [
            'name' => 'Cloud Computing',
        ]);
        $createCat->assertStatus(201);
        $this->assertDatabaseHas('categories', ['name' => 'Cloud Computing']);

        // المشرف ينشئ وسماً
        $createTag = $this->actingAs($this->admin, 'sanctum')->postJson('/api/tags', [
            'name' => 'Kubernetes',
        ]);
        $createTag->assertStatus(201);
        $this->assertDatabaseHas('tags', ['name' => 'Kubernetes']);
    }

    // ==========================================
    // 3. فحص Module 2: Posts Management & Soft Deletes
    // ==========================================

    public function test_user_can_create_post_with_file_upload_and_tags(): void
    {
        Storage::fake('public');
        $category = Category::factory()->create();
        $tags = Tag::factory(2)->create();

        $image = UploadedFile::fake()->create('cover.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($this->user1, 'sanctum')->postJson('/api/posts', [
            'title' => 'First Automated Post',
            'body' => 'Post body content with detailed explanation.',
            'category_id' => $category->id,
            'status' => 'published',
            'featured_image' => $image,
            'tags' => $tags->pluck('id')->toArray(),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('post.title', 'First Automated Post')
            ->assertJsonCount(2, 'post.tags');

        $this->assertDatabaseHas('posts', ['title' => 'First Automated Post']);
        $this->assertDatabaseHas('post_tag', ['tag_id' => $tags[0]->id]);
    }

    public function test_post_view_permissions_draft_vs_published(): void
    {
        $category = Category::factory()->create();

        // إنشاء مسودة تابعة للمستخدم الأول
        $draft = Post::factory()->draft()->create([
            'user_id' => $this->user1->id,
            'category_id' => $category->id,
        ]);

        // 1. الزائر غير المسجل لا يرى المسودة
        $this->getJson("/api/posts/{$draft->slug}")->assertStatus(403);

        // 2. مستخدم آخر لا يرى مسودة ليست له
        $this->actingAs($this->user2, 'sanctum')->getJson("/api/posts/{$draft->slug}")
            ->assertStatus(403);

        // 3. كاتب المقال نفسه يستطيع رؤية مسودته
        $this->actingAs($this->user1, 'sanctum')->getJson("/api/posts/{$draft->slug}")
            ->assertStatus(200);

        // 4. المشرف يستطيع رؤية مسودة أي كاتب
        $this->actingAs($this->admin, 'sanctum')->getJson("/api/posts/{$draft->slug}")
            ->assertStatus(200);
    }

    public function test_user_cannot_update_or_delete_others_post_but_admin_can(): void
    {
        $post = Post::factory()->create(['user_id' => $this->user1->id]);

        // مستخدم 2 يحاول تعديل مقال مستخدم 1 (403)
        $this->actingAs($this->user2, 'sanctum')->postJson("/api/posts/{$post->id}", [
            '_method' => 'PUT',
            'title' => 'Hacked Title',
        ])->assertStatus(403);

        // كاتب المقال يعدل مقاله بنجاح
        $this->actingAs($this->user1, 'sanctum')->postJson("/api/posts/{$post->id}", [
            '_method' => 'PUT',
            'title' => 'Author Updated Title',
        ])->assertStatus(200);

        // المشرف يستطيع حذف المقال (Soft Delete)
        $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/posts/{$post->id}")
            ->assertStatus(200);

        // التأكد من بقاء المقال في قاعدة البيانات ولكن محذوف برمجياً (Soft Deleted)
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }

    // ==========================================
    // 4. فحص Module 4: Comments & Moderation
    // ==========================================

    public function test_comment_lifecycle_creation_and_moderation(): void
    {
        Queue::fake();

        $post = Post::factory()->published()->create();

        // 1. مستخدم 1 يضيف تعليقاً
        $createResponse = $this->actingAs($this->user1, 'sanctum')
            ->postJson("/api/posts/{$post->id}/comments", [
                'body' => 'Great insights, thanks for sharing!',
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('comment.status', 'pending');

        $commentId = $createResponse->json('comment.id');

        // 2. مستخدم عادي لا يرى التعليق المعلق
        $indexResponse = $this->actingAs($this->user2, 'sanctum')
            ->getJson("/api/posts/{$post->id}/comments");
        $indexResponse->assertJsonCount(0, 'data');

        // 3. مستخدم عادي لا يستطيع اعتماد التعليق (403)
        $this->actingAs($this->user2, 'sanctum')
            ->patchJson("/api/comments/{$commentId}/moderate", ['status' => 'approved'])
            ->assertStatus(403);

        // 4. المشرف يعتمد التعليق
        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/comments/{$commentId}/moderate", ['status' => 'approved'])
            ->assertStatus(200)
            ->assertJsonPath('comment.status', 'approved');

        // 5. الآن التعليق يظهر للجميع
        $this->getJson("/api/posts/{$post->id}/comments")
            ->assertJsonCount(1, 'data');
    }
}
