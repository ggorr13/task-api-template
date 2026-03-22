<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use App\DTOs\Project\ProjectDTO;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function __construct(
        protected ProjectService $projectService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ProjectResource::collection($this->projectService->getAllUserProjects());
    }

    public function store(ProjectRequest $request): ProjectResource
    {
        $dto = ProjectDTO::fromRequest($request->validated());
        return new ProjectResource($this->projectService->createProject($dto->toArray()));
    }

    public function show(int $id): ProjectResource
    {
        $project = $this->projectService->getProjectById($id);

        if (!$project) {
            abort(404);
        }

        Gate::authorize('manage', $project);

        return new ProjectResource($project);
    }

    public function update(ProjectRequest $request, int $id): ProjectResource
    {
        $project = $this->projectService->getProjectById($id);

        if (!$project) {
            abort(404);
        }

        Gate::authorize('manage', $project);
        $dto = ProjectDTO::fromRequest($request->validated());
        $updated = $this->projectService->updateProject($id, $dto->toArray());

        return new ProjectResource($updated);
    }

    public function destroy(int $id): Response
    {
        $project = $this->projectService->getProjectById($id);

        if (!$project) {
            abort(404);
        }

        Gate::authorize('manage', $project);
        $this->projectService->deleteProject($id);

        return response()->noContent();
    }
}
