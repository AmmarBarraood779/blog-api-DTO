<?php

namespace App\Actions\Comments;

use App\Models\Comment;

class DeleteCommentAction
{
    public function execute(Comment $comment): void
    {
        $comment->delete();
    }
}
