<?php

use App\Http\Controllers\Api\{
    AuthController,
    CommentController,
    ProjectController,
    TaskController,
    NotificationController
};
use Illuminate\Support\Facades\Route;

// Public Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/unseen', [NotificationController::class, 'index']);
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    });

    // Projects
    Route::apiResource('projects', ProjectController::class);

    //Tasks
    Route::apiResource('tasks', TaskController::class);
    Route::middleware('throttle:60,1')->group(function () {
        Route::patch('/tasks/{task}', [TaskController::class, 'update']);
        Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    });

    Route::apiResource('tasks.comments', CommentController::class)
        ->parameters([
            'tasks' => 'taskId',
            'comments' => 'commentId'
        ]);
});
