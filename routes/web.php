<?php

declare(strict_types=1);

use App\Http\Controllers\CommentController;
use App\Http\Controllers\IdeaController;
use App\Http\Controllers\IdeaImageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/ideas');

Route::middleware('auth')->group(function () {
    Route::get('/ideas', [IdeaController::class, 'index'])->name('ideas.index');
    Route::post('/ideas', [IdeaController::class, 'store'])->name('ideas.store');
    Route::patch('/ideas/{idea}', [IdeaController::class, 'update'])->name('ideas.update');
    Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->name('ideas.destroy');
    Route::get('/ideas/{idea}', [IdeaController::class, 'show'])->name('ideas.show');
    Route::get('/ideas/{idea}/edit', [IdeaController::class, 'edit'])->name('ideas.edit');

    // misc
    Route::patch('/steps/{step}', [StepController::class, 'update'])->name('steps.update');
    Route::delete('/ideas/{idea}/image', [IdeaImageController::class, 'destroy'])->name('ideas.delete-image');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.delete');
    Route::delete('/profile/image/delete', [ProfileController::class, 'deleteImage'])->name('profile.delete-profile-image');

    // comments
    Route::post('/ideas/{idea}/comments', [CommentController::class, 'store'])->name('ideas.add-comment');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create']);
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [SessionController::class, 'create'])->name('login'); // NAMED ROUTE for login default route (redirects to this route if user is not authenticated)
    Route::post('/login', [SessionController::class, 'store']);
});

Route::delete('/logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');
