<?php

namespace App\Actions\Comments;

use App\DTOs\Comments\CreateCommentDTO;
use App\Jobs\ModerateCommentJob;
use App\Models\Comment;
use App\Models\User;

class AddCommentAction
{
    public function execute(User $user, CreateCommentDTO $dto): Comment
    {
        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $dto->postId,
            'body' => $dto->body,
            'status' => 'pending',
        ]);
        ModerateCommentJob::dispatch($comment);

        return $comment->load('user');
    }
}
