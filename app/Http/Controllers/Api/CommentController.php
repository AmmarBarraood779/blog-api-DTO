<?php

namespace App\Http\Controllers\Api;

use App\Actions\Comments\AddCommentAction;
use App\Actions\Comments\DeleteCommentAction;
use App\Actions\Comments\ModerateCommentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\ModerateCommentRequest;
use App\Http\Requests\Comments\StoreCommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function index(Request $request, Post $post): AnonymousResourceCollection
    {
        $comments = $post->comments()
            ->with('user')
            ->visibleTo($request->user('sanctum'))
            ->latest()
            ->paginate(15);

        return CommentResource::collection($comments);
    }

    public function store(StoreCommentRequest $request, Post $post, AddCommentAction $action): JsonResponse
    {
        $comment = $action->execute($request->user(), $request->toDTO($post->id));

        return response()->json([
            'message' => 'تمت إضافة التعليق بنجاح وهو بانتظار موافقة المشرف.',
            'comment' => new CommentResource($comment),
        ], 201);
    }

    public function moderate(ModerateCommentRequest $request, Comment $comment, ModerateCommentAction $action): JsonResponse
    {
        $this->authorize('moderate', $comment);

        $updatedComment = $action->execute($comment, $request->toDTO());

        return response()->json([
            'message' => 'تم تحديث حالة التعليق بنجاح.',
            'comment' => new CommentResource($updatedComment),
        ]);
    }

    public function destroy(Comment $comment, DeleteCommentAction $action): JsonResponse
    {
        $this->authorize('delete', $comment);

        $action->execute($comment);

        return response()->json([
            'message' => 'تم حذف التعليق بنجاح.',
        ]);
    }
}
