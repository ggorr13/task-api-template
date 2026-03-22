<?php

namespace App\DTOs\Project;

readonly class ProjectDTO
{
    public function __construct(
        public string  $title,
        public ?string $description = null,
    )
    {
    }

    public static function fromRequest(array $validated): self
    {
        return new self(
            title: $validated['title'],
            description: $validated['description'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
