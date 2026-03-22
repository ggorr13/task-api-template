<?php

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskNotification;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->task = Task::withoutEvents(function () {
        return Task::factory()->create(['user_id' => $this->user->id]);
    });

    Sanctum::actingAs($this->user);
});

it('can fetch unread notifications via resource', function () {

    $this->user->notify(new TaskNotification($this->task, 'created'));

    $response = $this->getJson('/api/notifications/unseen');

    // 3. Assert correct structure and count
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'type',
                    'data',
                    'read_at',
                    'created_at'
                ]
            ]
        ])
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.data.task_id', $this->task->id);
});

it('returns an empty collection when there are no unread notifications', function () {
    // Database is clean because of withoutEvents in beforeEach
    $response = $this->getJson('/api/notifications/unseen');

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});

it('can mark a specific notification as read', function () {
    // 1. Generate a notification
    $this->user->notify(new TaskNotification($this->task, 'updated'));
    $notification = $this->user->unreadNotifications->first();

    // 2. Execute patch request
    $response = $this->patchJson("/api/notifications/{$notification->id}/read");

    // 3. Assertions
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);

    // Database verification
    expect($this->user->fresh()->unreadNotifications)->toHaveCount(0);
    expect($this->user->fresh()->notifications()->first()->read_at)->not->toBeNull();
});

it('returns 404 when marking a non-existent notification as read', function () {
    // Use a valid UUID format to avoid potential DB driver casting errors
    $response = $this->patchJson("/api/notifications/00000000-0000-0000-0000-000000000000/read");

    $response->assertStatus(404);
});
