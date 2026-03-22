<?php

namespace App\DTOs\Task;

readonly class TaskDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $status,
        public readonly ?string $due_date,
        public readonly int $project_id,
        public readonly int $user_id
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            status: $data['status'] ?? 'todo',
            due_date: $data['due_date'] ?? null,
            project_id: (int) $data['project_id'],
            user_id: (int) auth()->id()
        );
    }

    public function toArray(): array
    {
        return [
            'title'      => $this->title,
            'description'=> $this->description,
            'status'     => $this->status,
            'due_date'   => $this->due_date,
            'project_id' => $this->project_id,
            'user_id'    => $this->user_id,
        ];
    }
}
