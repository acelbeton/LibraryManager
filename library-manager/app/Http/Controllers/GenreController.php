<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\GenreTranslation;
use App\Models\Language;
use App\Traits\GetCachedData;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{

    use GetCachedData;

    public function index() {
        try {
            $genres = Genre::all();
            $languages = Language::all();
            $genreTranslations = GenreTranslation::all();

            return view('genres.genre-index', compact('genres', 'genreTranslations', 'languages'));
        } catch (Exception $e) {
            Log::error('Error fetching genres: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while retrieving genres.');
        }
    }

    public function store(Request $request) {

        try {
            $validatedData = $request->validate([
                'name' => 'required|unique:genres|max:255',
            ]);

            Genre::create([
                'name' => $validatedData['name'],
            ]);

            $this->refreshCache('genres', Genre::class);
            $genres = Genre::all();
            $languages = Language::all();
            $genreTranslations = GenreTranslation::all();

            $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Genre added successfully'], 201);
        } catch (QueryException $e) {
            Log::error('Database error when adding genre: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when adding genre: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'genre_id' => 'required|exists:genres,id',
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        try {
            $validatedData = $validator->validated();

            $genre = Genre::findOrFail($validatedData['genre_id']);
            $genre->name = $validatedData['name'];
            $genre->save();
            $this->refreshCache('genres', Genre::class);
            $genres = Genre::all();
            $languages = Language::all();
            $genreTranslations = GenreTranslation::all();
            $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();
            return response()->json(['success' => true, 'html' => $html, 'message' => 'Genre updated successfully']);
        } catch (ModelNotFoundException $e) {
            Log::error('Genre not found for updating: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Genre not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error when updating genre: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when updating genre: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), ['id' => 'required|exists:genres,id']);

        if($validator->fails()) {
            return response()->json(['success' => false, 'errors' => 'There was an error with the deletion'], 422);
        }

        try {
            $genre = Genre::findOrFail($request->id);


            if ($genre->books()->count() > 0) {
                $genres = Genre::all();
                $languages = Language::all();
                $genreTranslations = GenreTranslation::all();

                $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();

                return response()->json([
                    'success' => false,
                    'html' => $html,
                    'error' => 'This genre cannot be deleted because it is associated with one or more books.'
                ], 422);
            }

            $genre->delete();

            GenreTranslation::where('genre_id', $request->id)->delete();

            $this->refreshCache('genres', Genre::class);

            $genres = Genre::all();
            $languages = Language::all();
            $genreTranslations = GenreTranslation::all();

            $html = view('partials.genresList', compact('genres', 'languages', 'genreTranslations'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Genre deleted successfully']);

        } catch (ModelNotFoundException $e) {
            Log::error('Genre not found for deletion: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'Genre not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error when deleting genre: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when deleting genre: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }



    public function getTranslation($genreId, $languageId)
    {
        try {
            $translation = GenreTranslation::where('genre_id', $genreId)
                ->where('language_id', $languageId)
                ->first();

            return response()->json(['translation' => $translation]);
        } catch (QueryException $e) {
            Log::error('Database error when fetching translation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when fetching translation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function suggestions(Request $request) {
        try {
            $searchTerm = $request->input('search_term');
            $results = Genre::where('name', 'LIKE', "%{$searchTerm}%")->get();

            $html = view('partials.suggestionsList', compact('results'))->render();

            return response()->json(['html' => $html]);
        } catch (QueryException $e) {
            Log::error('Database error when fetching suggestions: ' . $e->getMessage());
            return response()->json(['html' => '']);
        } catch (Exception $e) {
            Log::error('Unexpected error when fetching suggestions: ' . $e->getMessage());
            return response()->json(['html' => '']);
        }
    }
}
