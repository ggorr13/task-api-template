<?php

namespace App\Services;

use App\DTOs\Task\CommentDTO;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CommentService
{
    public function __construct(
        protected CommentRepositoryInterface $repository
    ) {}

    public function getCommentsByTaskId(int $taskId): Collection
    {
        return Cache::remember("task_{$taskId}_comments", 3600, function () use ($taskId) {
            return $this->repository->getByTaskId($taskId);
        });
    }

    public function store(CommentDTO $dto): Comment
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, CommentDTO $dto): Comment
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function find(int $id): Comment
    {
        return $this->repository->findById($id);
    }
}
