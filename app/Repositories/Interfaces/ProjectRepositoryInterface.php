<?php

namespace App\Repositories\Interfaces;

use App\Models\Project;

interface ProjectRepositoryInterface
{
    public function paginateForUser(int $userId, int $perPage = 15);

    public function find(int $id): ?Project;

    public function create(array $data): ?Project;

    public function update(int $id, array $data): ?Project;

    public function delete(int $id): bool;
}
