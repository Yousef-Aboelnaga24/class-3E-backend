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

/*
|--------------------------------------------------------------------------
| Public Auth
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/email/verify/{id}', [AuthController::class, 'verifyEmail'])
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Protected
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum'])->group(function () {

    /*
    | Auth
    */
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);

    /*
    | Profile (self only)
    */
    Route::post('/profile', [ProfileController::class, 'update']);

    /*
    | Users (public view inside auth)
    */
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::get('/users/{id}/posts', [UserController::class, 'posts']);

    /*
    | ADMIN ONLY USERS MANAGEMENT
    */
    Route::middleware('role:admin')->group(function () {

        Route::get('/users', [UserController::class, 'index']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);

        // 🔥 role update
        Route::post('/users/{id}/role', [UserController::class, 'updateRole']);

        // class codes
        Route::post('/class-codes', [ClassCodeController::class, 'store']);
        Route::get('/class-codes', [ClassCodeController::class, 'index']);
        Route::delete('/class-codes/{classCode}', [ClassCodeController::class, 'destroy']);

        // timeline
        Route::post('/timeline', [TimelineEventController::class, 'store']);
    });

    /*
    | Timeline (public view inside auth)
    */
    Route::get('/timeline', [TimelineEventController::class, 'index']);

    /*
    | Confessions
    */
    Route::get('/confessions', [ConfessionController::class, 'index']);
    Route::post('/confessions', [ConfessionController::class, 'store']);

    /*
    | Posts (student + admin)
    */
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);

    Route::post('/posts', [PostController::class, 'store'])
        ->middleware('role:student,admin');

    Route::put('/posts/{post}', [PostController::class, 'update'])
        ->middleware('role:student,admin');

    Route::delete('/posts/{post}', [PostController::class, 'destroy'])
        ->middleware('role:student,admin');

    /*
    | Comments
    */
    Route::get('/posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{post}/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    /*
    | Reactions
    */
    Route::post('/posts/{post}/react', [ReactionController::class, 'toggle']);

    /*
    | Messages
    */
    Route::get('/messages/received', [MessageController::class, 'received']);
    Route::get('/messages/sent', [MessageController::class, 'sent']);
    Route::post('/messages', [MessageController::class, 'store']);

    /*
    | Notifications
    */
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
