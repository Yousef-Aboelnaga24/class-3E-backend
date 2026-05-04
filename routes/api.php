<?php

use App\Http\Controllers\{
    AuthController,
    ClassCodeController,
    CommentController,
    ConfessionController,
    MessageController,
    NotificationController,
    PostController,
    ProfileController,
    ReactionController,
    TimelineEventController,
    UserController
};
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail'])
//     ->name('verification.verify');

Route::middleware(['auth:sanctum'])->group(function () {

    // 🔐 auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    // Route::post('/email/resend', [AuthController::class, 'resendVerification']);

    // 👤 profile (self)
    Route::post('/profile', [ProfileController::class, 'update']);

    // 👥 users (students for users, all for admin)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/{id}/posts', [UserController::class, 'posts']);

    // 👑 ADMIN ONLY
    Route::middleware('role:admin')->group(function () {

        Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/{id}/role', [UserController::class, 'updateRole']);

        Route::post('/class-codes', [ClassCodeController::class, 'store']);
        Route::get('/class-codes', [ClassCodeController::class, 'index']);
        Route::delete('/class-codes/{classCode}', [ClassCodeController::class, 'destroy']);

    });

    Route::middleware('role:student,admin')->group(function () {
        Route::post('/timeline', [TimelineEventController::class, 'store']);
        Route::put('/timeline/{timelineEvent}', [TimelineEventController::class, 'update']);
        Route::delete('/timeline/{timelineEvent}', [TimelineEventController::class, 'destroy']);
    });

    // 📌 timeline (read)
    Route::get('/timeline', [TimelineEventController::class, 'index']);

    // 💬 confessions
    Route::get('/confessions', [ConfessionController::class, 'index']);
    Route::post('/confessions', [ConfessionController::class, 'store']);

    // 📝 posts
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);

    Route::post('/posts', [PostController::class, 'store'])
        ->middleware('role:student,admin');

    Route::put('/posts/{post}', [PostController::class, 'update'])
        ->middleware('role:student,admin');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->middleware('role:student,admin');

    // 💬 comments
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // ❤️ reactions
    Route::post('/posts/{post}/react', [ReactionController::class, 'toggle']);

    // 📩 messages
    Route::get('/messages/received', [MessageController::class, 'received']);
    Route::get('/messages/sent', [MessageController::class, 'sent']);
    Route::post('/messages', [MessageController::class, 'store']);

    // 🔔 notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
