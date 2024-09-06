<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Traits\GetCachedData;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    use GetCachedData;

    public function index() {
        try {
            $authors = Author::all();
            return view('author.author-index', compact('authors'));
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to retrieve authors list.'], 500);
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'bio' => 'nullable|string'
            ]);

            Author::create([
                'name' => $validatedData['name'],
                'bio' => $validatedData['bio']
            ]);

            $authors = Author::all();
            $this->refreshCache('authors', Author::class);

            $html = view('partials.authorsList', compact('authors'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Author added successfully'], 201);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to add author due to a database error.'], 500);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'author_id' => 'required|exists:authors,id',
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        try {

            $validatedData = $validator->validated();

            $author = Author::findOrFail($validatedData['author_id']);
            $author->name = $validatedData['name'];
            $author->bio = $validatedData['bio'];
            $author->save();
            $authors = Author::all();
            $this->refreshCache('authors', Author::class);
            $html = view('partials.authorsList', compact('authors'))->render();
            return response()->json(['success' => true, 'html' => $html, 'message' => 'Author updated successfully']);
        }  catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Author not found.'], 404);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to update author due to a database error.'], 500);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:authors,id',
        ]);

        try {

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
            }

            $author = Author::findorFail($request->get('id'));

            if ($author->books()->count() > 0) {

                $authors = Author::all();

                $html = view('partials.authorsList', compact('authors'))->render();

                return response()->json([
                    'success' => false,
                    'html' => $html,
                    'error' => 'This author cannot be deleted because it is associated with one or more books.'
                ], 422);
            }

            $author->delete();

            $authors = Author::all();
            $this->refreshCache('authors', Author::class);
            $html = view('partials.authorsList', compact('authors'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Author deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['success' => false, 'error' => 'Author not found.'], 404);
        } catch (QueryException $e) {
            return response()->json(['success' => false, 'error' => 'Failed to delete author due to a database error.'], 500);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function suggestions(Request $request)
    {
        try {
            $searchTerm = $request->input('search_term');
            $results = Author::where('name', 'LIKE', "%{$searchTerm}%")->get();

            $html = view('partials.suggestionsList', compact('results'))->render();

            return response()->json(['html' => $html]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'error' => 'Failed to retrieve suggestions.'], 500);
        }
    }
}
