<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use App\DTOs\Task\TaskDTO;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(protected TaskService $taskService) {}

    public function index(): AnonymousResourceCollection
    {
        $tasks = $this->taskService->getTasks(request()->all());
        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request): TaskResource
    {
        $dto = TaskDTO::fromRequest($request->validated());
        $task = $this->taskService->store($dto);

        return new TaskResource($task->load(['project', 'user']));
    }

    public function show(int $id): TaskResource
    {
        $task = $this->taskService->getTaskById($id);
        if (!$task) abort(404);

        Gate::authorize('manage', $task);

        return new TaskResource($task->load(['project', 'user']));
    }

    public function update(TaskRequest $request, int $id): TaskResource
    {
        $task = $this->taskService->getTaskById($id);
        if (!$task) abort(404);

        Gate::authorize('manage', $task);

        $dto = TaskDTO::fromRequest($request->validated());
        $updated = $this->taskService->update($id, $dto);

        return new TaskResource($updated->load(['project', 'user']));
    }

    public function destroy(int $id): JsonResponse
    {
        $task = $this->taskService->getTaskById($id);
        if (!$task) abort(404);

        Gate::authorize('manage', $task);
        $this->taskService->delete($id);

        return response()->json(['success' => true], 204);
    }
}
