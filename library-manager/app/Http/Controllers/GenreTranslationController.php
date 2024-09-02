<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\GenreTranslation;
use App\Models\Language;
use Illuminate\Http\Request;

class GenreTranslationController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'genre_id' => 'required|exists:genres,id',
            'language_id' => 'required|exists:languages,id',
            'translated_name' => 'required|string|max:255',
        ]);

        GenreTranslation::create($validatedData);

        $genres = Genre::all();
        $languages = Language::all();

        $html = view('partials.genresList', compact('genres', 'languages'))->render();

        return response()->json(['success' => true, 'html' => $html, 'message' => 'Translation added successfully']);
    }
}

