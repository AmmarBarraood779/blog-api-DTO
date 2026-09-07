<?php

namespace App\Http\Controllers\Api;

use App\Actions\Posts\CreatePostAction;
use App\Actions\Posts\DeletePostAction;
use App\Actions\Posts\FetchPostsAction;
use App\Actions\Posts\ForceDeletePostAction;
use App\Actions\Posts\RecordPostViewAction;
use App\Actions\Posts\RestorePostAction;
use App\Actions\Posts\UpdatePostAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Posts\FilterPostRequest;
use App\Http\Requests\Posts\StorePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    public function index(FilterPostRequest $request, FetchPostsAction $action): AnonymousResourceCollection
    {
        $posts = $action->execute(
            dto: $request->toDTO(),
            user: $request->user('sanctum')
        );

        return PostResource::collection($posts);
    }

    public function store(StorePostRequest $request, CreatePostAction $action): JsonResponse
    {
        $post = $action->execute($request->user(), $request->toDTO());

        return response()->json([
            'message' => 'تم إنشاء المقال بنجاح.',
            'post' => new PostResource($post),
        ], 201);
    }

    public function show(Request $request, Post $post, RecordPostViewAction $viewAction): PostResource
    {
        $user = $request->user('sanctum');
        Gate::forUser($user)->authorize('view', $post);

        $viewAction->execute($post, (string) $request->ip());

        $post->load(['user', 'category', 'tags']);

        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post, UpdatePostAction $action): JsonResponse
    {
        $this->authorize('update', $post);

        $updatedPost = $action->execute($post, $request->toDTO());

        return response()->json([
            'message' => 'تم تحديث المقال بنجاح.',
            'post' => new PostResource($updatedPost),
        ]);
    }

    public function destroy(Post $post, DeletePostAction $action): JsonResponse
    {
        $this->authorize('delete', $post);

        $action->execute($post);

        return response()->json([
            'message' => 'تم حذف المقال (Soft Delete) بنجاح.',
        ]);
    }

    public function trashed(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $posts = Post::onlyTrashed()
            ->when(! $user->is_admin, fn ($query) => $query->where('user_id', $user->id))
            ->with(['user', 'category', 'tags'])
            ->latest('deleted_at')
            ->paginate(10);

        return PostResource::collection($posts);
    }

    public function restore(Post $post, RestorePostAction $action): JsonResponse
    {
        $this->authorize('restore', $post);

        $action->execute($post);

        return response()->json([
            'message' => 'تمت استعادة المقال بنجاح.',
            'post' => new PostResource($post->load(['user', 'category', 'tags'])),
        ]);
    }

    public function forceDelete(Post $post, ForceDeletePostAction $action): JsonResponse
    {
        $this->authorize('forceDelete', $post);

        $action->execute($post);

        return response()->json([
            'message' => 'تم حذف المقال نهائياً من النظام.',
        ]);
    }
}
