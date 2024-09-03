<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\GenreTranslationController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::post('/books', [BookController::class, 'store'])->name('books.store');

Route::post('/books/destroy', [BookController::class, 'destroy'])->name('books.destroy');

Route::post('/books/update', [BookController::class, 'update'])->name('books.update');

Route::get('/books/suggestions', [BookController::class, 'getSuggestions'])->name('books.suggestions');

Route::post('/books/search', [BookController::class, 'search'])->name('books.search');

Route::post('books/translate/add', [TranslationController::class, 'store'])->name('books.translate.add');

Route::get('/books/{book}/translate/{language}', [BookController::class, 'getTranslation']);

Route::get('/books/{book}/translation/{language}', [BookController::class, 'getTranslation']);

Route::get('/author/suggestions', [AuthorController::class, 'suggestions']);
Route::get('/genre/suggestions', [GenreController::class, 'suggestions']);
Route::get('/publisher/suggestions', [PublisherController::class, 'suggestions']);

Route::get('genres', [GenreController::class, 'index'])->name('genres.index');
Route::post('genres', [GenreController::class, 'store'])->name('genres.store');

Route::post('genres/translations', [GenreTranslationController::class, 'store'])->name('genres.translations.store');

Route::post('genres/update', [GenreController::class, 'update'])->name('genres.update');

Route::get('/genres/{genreId}/translations/{languageId}', [GenreController::class, 'getTranslation']);

Route::post('/genres/destroy', [GenreController::class, 'destroy'])->name('genres.destroy');

Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::post('authors', [AuthorController::class, 'store'])->name('authors.store');

Route::post('authors/update', [AuthorController::class, 'update'])->name('authors.update');
Route::post('/authors/destroy', [AuthorController::class, 'destroy'])->name('authors.destroy');

Route::get('/publishers', [PublisherController::class, 'index'])->name('publishers.index');
Route::post('publishers', [PublisherController::class, 'store'])->name('publishers.store');

Route::post('publishers/update', [PublisherController::class, 'update'])->name('publishers.update');
Route::post('/publishers/destroy', [PublisherController::class, 'destroy'])->name('publishers.destroy');

Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
Route::post('languages', [LanguageController::class, 'store'])->name('languages.store');

Route::post('languages/update', [LanguageController::class, 'update'])->name('languages.update');
Route::post('/languages/destroy', [LanguageController::class, 'destroy'])->name('languages.destroy');


Route::get('/', function () {
    return view('welcome');
});
