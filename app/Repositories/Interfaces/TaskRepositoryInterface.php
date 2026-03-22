<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    public function filterTasks(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Task;

    public function create(array $data): ?Task;

    public function update(int $id, array $data): ?Task;

    public function delete(int $id): bool;
}
