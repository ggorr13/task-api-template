<?php

namespace App\DTOs\Project;

use Illuminate\Http\Request;

readonly class ProjectDTO
{
    public function __construct(
        public string  $title,
        public ?string $description = null,
    ) {}

    public static function fromRequest(array $validatedData): self
    {
        return new self(
            title: $validatedData['title'],
            description: $validatedData['description'] ?? null,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
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
