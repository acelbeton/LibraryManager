<?php

use App\Http\Controllers\BookController;
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

Route::get('/', function () {
    return view('welcome');
});
