<?php

namespace App\Observers;

use App\Models\Comment;
use Illuminate\Support\Facades\Cache;

class CommentObserver
{

    public function created(Comment $comment): void
    {
        $this->invalidateCache($comment);
    }

    public function updated(Comment $comment): void
    {
        $this->invalidateCache($comment);
    }

    public function deleted(Comment $comment): void
    {
        $this->invalidateCache($comment);
    }

    private function invalidateCache(Comment $comment): void
    {
        Cache::forget("task_{$comment->task_id}_comments");
    }
}
