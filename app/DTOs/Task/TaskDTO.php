<?php

namespace App\DTOs\Task;

readonly class TaskDTO
{
    public function __construct(
        public string  $title,
        public ?string $description,
        public string  $status,
        public ?string $due_date,
        public int     $project_id,
        public int     $user_id
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            title:       $validatedData['title'],
            description: $validatedData['description'] ?? null,
            status:      $validatedData['status'] ?? 'todo',
            due_date:    $validatedData['due_date'] ?? null,
            project_id:  (int) $validatedData['project_id'],
            user_id:     (int) ($validatedData['user_id'] ?? (int) auth()?->id())
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title:       $data['title'],
            description: $data['description'] ?? null,
            status:      $data['status'] ?? 'todo',
            due_date:    $data['due_date'] ?? null,
            project_id:  (int) $data['project_id'],
            user_id:     (int) $data['user_id']
        );
    }

    public function toArray(): array
    {
        return [
            'title'       => $this->title,
            'description' => $this->description,
            'status'      => $this->status,
            'due_date'    => $this->due_date,
            'project_id'  => $this->project_id,
            'user_id'     => $this->user_id,
        ];
    }
}
