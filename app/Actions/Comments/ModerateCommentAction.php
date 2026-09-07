<?php

namespace App\Actions\Comments;

use App\DTOs\Comments\ModerateCommentDTO;
use App\Models\Comment;

class ModerateCommentAction
{
    public function execute(Comment $comment, ModerateCommentDTO $dto): Comment
    {
        $comment->update([
            'status' => $dto->status,
        ]);

        return $comment->load('user');
    }
}
