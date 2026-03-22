<?php

use App\Models\User;
use App\Models\Task;
use App\Models\Comment;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->task = Task::factory()->create(['user_id' => $this->user->id]);
    Sanctum::actingAs($this->user);
});

it('can list comments for a task and verifies caching', function () {
    Comment::factory()->count(3)->create(['task_id' => $this->task->id]);

    $response = $this->getJson("/api/tasks/{$this->task->id}/comments");

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');

    // Verify cache is primed after index call
    expect(Cache::has("task_{$this->task->id}_comments"))->toBeTrue();
});

it('clears cache when a new comment is stored', function () {
    Cache::put("task_{$this->task->id}_comments", ['old_data']);

    $this->postJson("/api/tasks/{$this->task->id}/comments", [
        'body' => 'Adding a fresh comment'
    ])->assertStatus(201);

    // Observer should have wiped the cache
    expect(Cache::has("task_{$this->task->id}_comments"))->toBeFalse();
});

it('can show a specific comment by id', function () {
    $comment = Comment::factory()->create(['task_id' => $this->task->id]);

    $this->getJson("/api/tasks/{$this->task->id}/comments/{$comment->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.body', $comment->body);
});

it('prevents updating a comment owned by another user', function () {
    $stranger = User::factory()->create();
    $comment = Comment::factory()->create([
        'user_id' => $stranger->id,
        'task_id' => $this->task->id
    ]);

    $this->patchJson("/api/tasks/{$this->task->id}/comments/{$comment->id}", [
        'body' => 'Attempted Hack'
    ])->assertStatus(403);
});

it('successfully updates a comment and clears cache', function () {
    $comment = Comment::factory()->create([
        'user_id' => $this->user->id,
        'task_id' => $this->task->id,
        'body' => 'Old Body'
    ]);

    Cache::put("task_{$this->task->id}_comments", ['cached']);

    $this->patchJson("/api/tasks/{$this->task->id}/comments/{$comment->id}", [
        'body' => 'New Updated Body'
    ])->assertStatus(200);

    $this->assertDatabaseHas('comments', ['body' => 'New Updated Body']);
    expect(Cache::has("task_{$this->task->id}_comments"))->toBeFalse();
});

it('deletes a comment and verifies cleanup', function () {
    $comment = Comment::factory()->create([
        'user_id' => $this->user->id,
        'task_id' => $this->task->id
    ]);

    $this->deleteJson("/api/tasks/{$this->task->id}/comments/{$comment->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
});
