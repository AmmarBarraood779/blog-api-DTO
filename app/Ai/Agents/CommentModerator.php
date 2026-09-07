<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

class CommentModerator implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * التعليمات الموجهة للنموذج (System Instructions)
     */
    public function instructions(): string
    {
        return 'You are an automated AI content moderation engine for a professional blog platform. 
Your task is to analyze user comments and detect inappropriate content including:
1. Severe insults, hate speech, or harassment.
2. Scam, fraud, or commercial spam links.
3. Explicit or violent language.

Evaluate the comment strictly. If safe, mark is_safe as true. If unsafe, mark is_safe as false and provide a concise reason in Arabic explaining why it was rejected.';
    }

    /**
     * تحديد المخطط الدقيق لمخرجات الذكاء الاصطناعي عبر Fluent Builder
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'is_safe' => $schema->boolean()->description('True if the comment is safe to publish, false if inappropriate.'),
            'reason' => $schema->string()->nullable()->description('A brief rejection explanation in Arabic (1 sentence), or null if safe.'),
        ];
    }
}
