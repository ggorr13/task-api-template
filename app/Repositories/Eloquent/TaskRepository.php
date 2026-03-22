<?php

namespace App\Repositories\Eloquent;

use App\Filters\Task\TaskFilterManager;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        protected TaskFilterManager $filterManager
    ) {}

    public function filterTasks(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Task::query();

        return $this->filterManager->apply($query, $filters)
            ->with(['project', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Task
    {
        return Task::query()->find($id);
    }

    public function create(array $data): ?Task
    {
        return Task::query()->create($data);
    }

    public function update(int $id, array $data): ?Task
    {
        return Task::query()->updateOrCreate(['id' => $id], $data);
    }

    public function delete(int $id): bool
    {
        return Task::destroy($id);
    }
}
