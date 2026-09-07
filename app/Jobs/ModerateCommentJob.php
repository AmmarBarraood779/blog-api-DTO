<?php

namespace App\Jobs;

use App\Ai\Agents\CommentModerator;
use App\Models\Comment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\StructuredAgentResponse;

class ModerateCommentJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(public Comment $comment)
    {
        $this->onQueue('moderation');
    }

    public function handle(): void
    {
        try {
            $moderator = new CommentModerator;

            /** @var StructuredAgentResponse $response */
            $response = $moderator->prompt($this->comment->body);

            $result = $response->structured;

            if (! empty($result['is_safe'])) {
                $this->comment->update([
                    'status' => 'approved',
                    'moderation_reason' => null,
                ]);
            } else {
                $this->comment->update([
                    'status' => 'rejected',
                    'moderation_reason' => $result['reason'] ?? 'تم رفض المحتوى لمخالفته معايير النشر.',
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("فشل تدقيق التعليق رقم {$this->comment->id}: ".$e->getMessage());
            throw $e;
        }
    }
}
