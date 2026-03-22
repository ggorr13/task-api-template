<?php

namespace App\Repositories\Eloquent;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Support\Collection;

class CommentRepository implements CommentRepositoryInterface
{
    public function getByTaskId(int $taskId): Collection
    {
        return Comment::query()
            ->where('task_id', $taskId)
            ->with('user')
            ->latest()
            ->get();
    }

    public function create(array $data): Comment
    {
        return Comment::query()->create($data);
    }

    public function update(int $id, array $data): Comment
    {
        return Comment::query()->updateOrCreate(['id' => $id], $data);
    }

    public function delete(int $id): bool
    {
        return Comment::destroy($id);
    }

    public function findById(int $id): ?Comment
    {
        return Comment::find($id);
    }
}
