<?php

namespace App\Services;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\DTOs\Task\TaskDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use App\Models\Task;

class TaskService
{
    private const CACHE_TAG = 'tasks';

    public function __construct(protected TaskRepositoryInterface $repository) {}

    public function getTasks(array $filters): LengthAwarePaginator
    {
        $userId = auth()->id();
        $page = request('page', 1);
        $key = "tasks_u{$userId}_" . md5(json_encode($filters) . "_p{$page}");

        return Cache::tags([self::CACHE_TAG, "user_{$userId}"])->remember(
            $key,
            600,
            fn() => $this->repository->filterTasks($filters)
        );
    }

    public function getTaskById(int $id): ?Task
    {
        return $this->repository->find($id);
    }

    public function store(TaskDTO $dto): Task
    {
        return $this->repository->create($dto->toArray());
    }

    public function update(int $id, TaskDTO $dto): Task
    {
        return $this->repository->update($id, $dto->toArray());
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
