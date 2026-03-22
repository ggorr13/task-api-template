<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\Interfaces\ProjectRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ProjectService
{
    public function __construct(
        protected ProjectRepositoryInterface $projectRepository
    ) {}

    public function getAllUserProjects(int $perPage = 15): LengthAwarePaginator
    {
        return $this->projectRepository->paginateForUser(Auth::id(), $perPage);
    }

    public function createProject(array $data): Project
    {
        $data['user_id'] = Auth::id();
        return $this->projectRepository->create($data);
    }

    public function getProjectById(int $id): ?Project
    {
        return $this->projectRepository->find($id);
    }

    public function updateProject(int $id, array $data): Project
    {
        return $this->projectRepository->update($id, $data);
    }

    public function deleteProject(int $id): bool
    {
        return $this->projectRepository->delete($id);
    }
}
