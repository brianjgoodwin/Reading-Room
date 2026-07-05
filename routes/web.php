<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('shelf.index'));

Route::middleware('auth')->group(function () {

    // Shelf
    Route::get('/shelf',            [ShelfController::class, 'index'])  ->name('shelf.index');
    Route::get('/shelf/{shelf}',    [ShelfController::class, 'show'])   ->name('shelf.show');
    Route::patch('/shelf/{shelf}',  [ShelfController::class, 'update']) ->name('shelf.update');
    Route::delete('/shelf/{shelf}', [ShelfController::class, 'destroy'])->name('shelf.destroy');

    // Books (search + add to shelf)
    Route::get('/books/search', [BookController::class, 'search'])->name('books.search');
    Route::post('/books',       [BookController::class, 'store']) ->name('books.store');

    // Posts (journal entries)
    Route::get('/posts',                         [PostController::class, 'index']) ->name('posts.index');
    Route::get('/shelf/{shelf}/posts/create',    [PostController::class, 'create'])->name('posts.create');
    Route::post('/shelf/{shelf}/posts',          [PostController::class, 'store']) ->name('posts.store');
    Route::get('/posts/{post}',                  [PostController::class, 'show'])  ->name('posts.show');
    Route::get('/posts/{post}/edit',             [PostController::class, 'edit'])  ->name('posts.edit');
    Route::patch('/posts/{post}',                [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}',               [PostController::class, 'destroy'])->name('posts.destroy');

    // Tags
    Route::get('/tags',            [TagController::class, 'index'])->name('tags.index');
    Route::get('/tags/{tag:slug}', [TagController::class, 'show']) ->name('tags.show');

    // Stats
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');

    // Goodreads import
    Route::get('/import',  [ImportController::class, 'create'])->name('import.create');
    Route::post('/import', [ImportController::class, 'store']) ->name('import.store');

    // Profile (Breeze)
    Route::get('/profile',    [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
