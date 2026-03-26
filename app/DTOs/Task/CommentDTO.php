<?php

namespace App\DTOs\Task;

readonly class CommentDTO
{
    public function __construct(
        public string $body,
        public int $task_id,
        public int $user_id,
    ) {}

    public static function fromRequest(array $validatedData, int $taskId): self
    {
        return new self(
            body: $validatedData['body'],
            task_id: $taskId,
            user_id: (int) auth()->id()
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            body: $data['body'],
            task_id: $data['task_id'],
            user_id: $data['user_id'],
        );
    }

    public function toArray(): array
    {
        return [
            'body' => $this->body,
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
        ];
    }
}
