<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CommentRequest;
use App\Http\Resources\CommentResource;
use App\DTOs\Task\CommentDTO;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    public function __construct(
        protected CommentService $service
    ) {}

    public function index(int $taskId): AnonymousResourceCollection
    {
        $comments = $this->service->getCommentsByTaskId($taskId);

        return CommentResource::collection($comments);
    }

    public function store(CommentRequest $request, int $taskId): CommentResource
    {
        $dto = CommentDTO::fromRequest($request->validated(), $taskId);
        $comment = $this->service->store($dto);

        return new CommentResource($comment);
    }

    public function show(int $commentId): CommentResource
    {
        $comment = $this->service->find($commentId);

        return new CommentResource($comment);
    }

    public function update(CommentRequest $request, int $taskId, int $commentId): CommentResource
    {
        $comment = $this->service->find($commentId);
        Gate::authorize('update', $comment);

        // 3. Update using DTO
        $dto = CommentDTO::fromRequest($request->validated(), $taskId);
        $updated = $this->service->update($commentId, $dto);

        return new CommentResource($updated);
    }

    public function destroy(int $commentId): JsonResponse
    {
        $comment = $this->service->find($commentId);
        Gate::authorize('delete', $comment);
        $this->service->delete($commentId);

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ], 200);
    }
}
