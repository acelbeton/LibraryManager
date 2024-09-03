<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Keyword;
use App\Models\Translation;
use App\Traits\GetCachedData;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TranslationController extends Controller
{
    use GetCachedData;

    public function store(Request $request) {

        $validator = Validator::make($request->all(),[
            'book_id' => 'required',
            'translated_title' => 'required|string|max:255',
            'translated_description' => 'min:3|max:1000',
            'keywords' => 'nullable|array',
            'keywords.*' => 'nullable|string|max:255',
            'language_id' => 'required|exists:languages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => 'Validation failed. Please check your input.'], 422);
        }

        try {
            $validatedData = $validator->validated();

            $translation = Translation::where('book_id', $validatedData['book_id'])
                ->where('language_id', $validatedData['language_id'])
                ->first();

            $book = Book::findOrFail($validatedData['book_id']);

            if ($translation) {
                if ($request->has('update_existing')) {
                    $translation->update([
                        'translated_title' => $validatedData['translated_title'],
                        'translated_description' => $validatedData['translated_description'],
                    ]);
                    $message = 'Translation updated successfully.';
                } else {
                    return response()->json([
                        'success' => true,
                        'exists' => true,
                        'message' => 'A translation for this book in the selected language already exists. Do you want to update it?',
                    ]);
                }
            } else {
                Translation::create($validatedData);
                $message = 'Translation added successfully.';
            }

            if (!empty($validatedData['keywords'])) {
                foreach ($validatedData['keywords'] as $keyword) {
                    $keywordRecord = Keyword::firstOrCreate([
                        'keyword' => $keyword,
                        'language_id' => $validatedData['language_id']
                    ]);

                    $book->keywords()->attach($keywordRecord->id);
                }
            }

            $books = Book::with(['author', 'genre', 'publisher'])->get();
            $cachedData = $this->getCachedData();
            $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

            return response()->json(['success' => true, 'message' => $message, 'html' => $html]);

        } catch (ModelNotFoundException $e) {
            Log::error('Book not found for translation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Book not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error during translation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error during translation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }

    }
}
