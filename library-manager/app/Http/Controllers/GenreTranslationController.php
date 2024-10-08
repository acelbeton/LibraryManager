<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\GenreTranslation;
use App\Models\Language;
use App\Traits\GetCachedData;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GenreTranslationController extends Controller
{
    use GetCachedData;

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'genre_id' => 'required|exists:genres,id',
                'language_id' => 'required|exists:languages,id',
                'translated_name' => 'required|string|max:255',
            ]);

            GenreTranslation::updateOrCreate(
                [
                    'genre_id' => $validatedData['genre_id'],
                    'language_id' => $validatedData['language_id']
                ],
                ['translated_name' => $validatedData['translated_name']]
            );

            $this->refreshCache('genreTranslations', GenreTranslation::class);

            $genres = Genre::all();
            $languages = Language::all();

            $html = view('partials.genresList', compact('genres', 'languages'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Translation added successfully']);
        } catch (QueryException $e) {
            Log::error('Database error when adding genre translation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when adding genre translation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }
}

