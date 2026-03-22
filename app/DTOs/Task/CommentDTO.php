<?php

namespace App\DTOs\Task;

readonly class CommentDTO
{
    public function __construct(
        public readonly string $body,
        public readonly int $task_id,
        public readonly int $user_id
    ) {}

    public static function fromRequest(array $data, int $taskId): self
    {
        return new self(
            body: $data['body'],
            task_id: $taskId,
            user_id: (int) auth()->id()
        );
    }

    public function toArray(): array
    {
        return [
            'body'    => $this->body,
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
        ];
    }
}
