<?php

namespace App\Repositories\Eloquent;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function findByUser(int $userId): Collection
    {
        return Project::query()->where('user_id', $userId)->get();
    }

    public function paginateForUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Project::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function find(int $id): ?Project
    {
        return Project::query()->find($id);
    }

    public function create(array $data): ?Project
    {
        return Project::query()->create($data);
    }

    public function update(int $id, array $data): ?Project
    {
        return Project::query()->updateOrCreate(['id' => $id], $data);
    }

    public function delete(int $id): bool
    {
        return Project::destroy($id);
    }
}
