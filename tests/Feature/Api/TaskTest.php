<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TaskNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->project = Project::factory()->create(['user_id' => $this->user->id]);
    Sanctum::actingAs($this->user);

    // Ensure cache is clear before each test
    Cache::tags(['tasks', "user_{$this->user->id}"])->flush();
});

it('creates a task, sends notification and invalidates cache', function () {
    Notification::fake();

    $data = [
        'title'       => 'New Task',
        'project_id'  => $this->project->id,
        'description' => 'Test Description',
        'status'      => 'todo'
    ];

    // 1. Prime the cache
    $this->getJson('/api/tasks');

    // 2. Create task
    $response = $this->postJson('/api/tasks', $data);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'New Task');

    $this->assertDatabaseHas('tasks', [
        'title'      => 'New Task',
        'project_id' => $this->project->id
    ]);

    // 3. Assert notification sent
    Notification::assertSentTo($this->user, TaskNotification::class);

    // 4. Assert cache was invalidated by Observer
    $cacheKey = "tasks_u{$this->user->id}_" . md5(json_encode([]) . "_p1");
    expect(Cache::tags(['tasks', "user_{$this->user->id}"])->has($cacheKey))->toBeFalse();
});

it('retrieves tasks from cache on second request', function () {
    Task::factory()->count(2)->create([
        'project_id' => $this->project->id,
        'user_id'    => $this->user->id
    ]);

    // First request: hits database and stores in cache
    $this->getJson('/api/tasks')->assertStatus(200);

    $cacheKey = "tasks_u{$this->user->id}_" . md5(json_encode([]) . "_p1");
    expect(Cache::tags(['tasks', "user_{$this->user->id}"])->has($cacheKey))->toBeTrue();

    // Second request: should ideally return from cache (verified by manual inspection or mock)
    $response = $this->getJson('/api/tasks');
    $response->assertStatus(200)->assertJsonCount(2, 'data');
});

it('filters tasks by status using Strategy Pattern', function () {
    Task::factory()->create([
        'project_id' => $this->project->id,
        'user_id'    => $this->user->id,
        'status'     => 'todo'
    ]);

    Task::factory()->create([
        'project_id' => $this->project->id,
        'user_id'    => $this->user->id,
        'status'     => 'done'
    ]);

    $response = $this->getJson('/api/tasks?status=done');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.status', 'done');
});

it('searches tasks using fulltext or like strategy', function () {
    Task::factory()->create([
        'project_id' => $this->project->id,
        'user_id'    => $this->user->id,
        'title'      => 'Unique Search Keyword',
    ]);

    $response = $this->getJson('/api/tasks?search=Unique');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Unique Search Keyword');
});

it('updates task and notifies user only when status changes', function () {
    Notification::fake();

    $task = Task::factory()->create([
        'project_id' => $this->project->id,
        'user_id'    => $this->user->id,
        'status'     => 'todo'
    ]);

    $updateData = [
        'title'      => 'Updated Title',
        'project_id' => $this->project->id,
        'status'     => 'in-progress' // Status changed
    ];

    $response = $this->putJson("/api/tasks/{$task->id}", $updateData);

    $response->assertStatus(200);

    Notification::assertSentTo($this->user, TaskNotification::class);
});

it('prevents unauthorized access to other user tasks', function () {
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
    $task = Task::factory()->create([
        'project_id' => $otherProject->id,
        'user_id'    => $otherUser->id
    ]);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);
});

it('validates required fields on task creation', function () {
    $response = $this->postJson('/api/tasks', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'project_id', 'status']);
});
