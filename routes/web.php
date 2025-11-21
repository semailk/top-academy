<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\PostCommentController;
use App\Http\Controllers\Web\PostController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\VerifyController;
use Illuminate\Support\Facades\Route;

//use Illuminate\Support\Facades\Auth;


// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth routes
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::get('user-reset-password/{user}/{hash}', [AuthController::class, 'userPasswordReset'])->name('user.password.reset');
Route::post('change-password/{user}', [AuthController::class, 'changePassword'])->name('change.password');
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset.post');

// Protected routes
Route::middleware('auth')->group(function () {
    // Posts
    Route::resource('posts', PostController::class);

    //POST COMMENT
    Route::post('comments', [PostCommentController::class, 'store'])->name('comments.store');
    Route::patch('comments/{postComment}', [PostCommentController::class, 'update'])->name('comments.update');
    Route::delete('comments/{postComment}', [PostCommentController::class, 'destroy'])->name('comments.destroy');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // Profile
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/avatar-remove', [UserController::class, 'removeAvatar'])->name('avatar.remove');

    // Category
    Route::get('/show/{category}', [CategoryController::class, 'show'])->name('category.show');

    // About
    Route::get('/about', function () {
        return view('about');
    })->name('about');
    // Прямой роут для аватарок
    Route::get('/avatar/{filename}', function ($filename) {
        $path = storage_path('app/public/avatars/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $mime = mime_content_type($path);
        $file = file_get_contents($path);

        return response($file, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Length', filesize($path));
    })->name('avatar.direct');
});

Route::post('/email/verify/request', [VerifyController::class, 'send'])
    ->middleware('auth:sanctum');

Route::get('/email/verify/{id}/{hash}', [VerifyController::class, 'verify'])
    ->name('verification.verify');
