<?php

namespace App\Observers;

use App\Models\Task;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Cache;

class TaskObserver
{
    public function created(Task $task): void
    {
        $task->user->notify(new TaskNotification($task, 'created/assigned'));
        $this->clearCache($task);
    }

    public function updated(Task $task): void
    {
        if ($task->isDirty(['status', 'due_date'])) {
            $task->user->notify(new TaskNotification($task, 'updated'));
        }
        $this->clearCache($task);
    }

    public function deleted(Task $task): void
    {
        $this->clearCache($task);
    }

    private function clearCache(Task $task): void
    {
        $userId = auth()->id() ?? $task->user_id;
        Cache::tags(['tasks', "user_{$userId}"])->flush();
    }
}
