<?php

use App\Http\Controllers\PhraseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'is_active'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard')->with('user', Auth::user());
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::get('/phrases', function () {
        return Inertia::render('Phrases');
    })->middleware(['auth', 'verified'])->name('phrases');

    Route::get('/myphrases', function () {
        return Inertia::render('MyPhrases');
    })->middleware(['auth', 'verified'])->name('myphrases');

    Route::get('/getphrases/{count}', function ($count) {
        return Inertia::render('PhrasesList', ['count' => $count]);
    })->middleware(['auth', 'verified'])->name('phrasesList');

    Route::post('/phrases/favorite', [PhraseController::class, 'toggleFavorite']);
    Route::get('/getmyphrases', [PhraseController::class, 'myPhrases']);
    Route::post('/deletephrase', [PhraseController::class, 'deletePhrase']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('admin')->group(function () {
    Route::get('/phrasesAll', [PhraseController::class, 'phrasesAll']);
    Route::post('/disable-user/{id}', [UserController::class, 'disableUser']);
    Route::post('/allow-user/{id}', [UserController::class, 'allowUser']);
});

require __DIR__.'/auth.php';
