<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    Sanctum::actingAs($this->user);
});

it('can list only user specific projects', function () {
    Project::factory()->count(3)->create(['user_id' => $this->user->id]);
    Project::factory()->count(2)->create();

    // Act
    $response = $this->getJson('/api/projects');

    // Assert
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

it('can create a project', function () {
    $data = [
        'title' => 'New Architecture Project',
        'description' => 'Detailed description of the task api.'
    ];

    $response = $this->postJson('/api/projects', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', $data['title']);

    $this->assertDatabaseHas('projects', [
        'title' => $data['title'],
        'user_id' => $this->user->id
    ]);
});

it('can show a specific project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->getJson("/api/projects/{$project->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $project->id);
});

it('cannot view another users project', function () {
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $response = $this->getJson("/api/projects/{$project->id}");

    $response->assertStatus(403); // Forbidden
});

it('can update a project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);
    $updateData = ['title' => 'Updated Title'];

    $response = $this->putJson("/api/projects/{$project->id}", $updateData);

    $response->assertStatus(200);
    $this->assertDatabaseHas('projects', [
        'id' => $project->id,
        'title' => 'Updated Title'
    ]);
});

it('can delete a project', function () {
    $project = Project::factory()->create(['user_id' => $this->user->id]);

    $response = $this->deleteJson("/api/projects/{$project->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});
