<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\GenreTranslation;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    public function index() {
        $genres = Genre::all();
        $languages = Language::all();
        $genreTranslations = GenreTranslation::all();

        return view('genres.genre-index', compact('genres', 'genreTranslations', 'languages'));
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|unique:genres|max:255',
        ]);

        Genre::create([
            'name' => $validatedData['name'],
        ]);

        $genres = Genre::all();
        $languages = Language::all();
        $genreTranslations = GenreTranslation::all();

        $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();

        return response()->json(['success' => true, 'html' => $html, 'message' => 'Genrecd added successfully']);
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'genre_id' => 'required|exists:genres,id',
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        $validatedData = $validator->validated();

        $genre = Genre::findOrFail($validatedData['genre_id']);
        $genre->name = $validatedData['name'];
        $genre->save();
        $genres = Genre::all();
        $languages = Language::all();
        $genreTranslations = GenreTranslation::all();
        $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();
        return response()->json(['success' => true, 'html' => $html, 'message' => 'Genrecd updated successfully']);
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), ['id' => 'required|exists:genres,id']);

        if($validator->fails()) {
            return response()->json(['success' => false, 'errors' => 'There was an error with the deletion'], 422);
        }

        $genre = Genre::findOrFail($request->id);


        if ($genre->books()->count() > 0) {
            $genres = Genre::all();
            $languages = Language::all();
            $genreTranslations = GenreTranslation::all();

            $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();

            return response()->json([
                'success' => false,
                'html' => $html,
                'errors' => 'This genre cannot be deleted because it is associated with one or more books.'
            ], 422);
        }

        $genre->delete();

        GenreTranslation::where('genre_id', $request->id)->delete();

        $genres = Genre::all();
        $languages = Language::all();
        $genreTranslations = GenreTranslation::all();

        $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();

        return response()->json(['success' => true, 'html' => $html, 'message' => 'Genre deleted successfully']);
    }



    public function getTranslation($genreId, $languageId)
    {
        $translation = GenreTranslation::where('genre_id', $genreId)
            ->where('language_id', $languageId)
            ->first();

        return response()->json(['translation' => $translation]);
    }

    public function suggestions(Request $request)
    {
        $searchTerm = $request->input('search_term');
        $results = Genre::where('name', 'LIKE', "%{$searchTerm}%")->get();

        $html = view('partials.suggestionsList', compact('results'))->render();

        return response()->json(['html' => $html]);
    }
}
