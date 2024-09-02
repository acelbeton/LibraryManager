<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index() {
        $authors = Author::all();
        return view('author.author-index', compact('authors'));
    }


    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'bio' => 'nullable|string'
        ]);

        Author::create([
            'name' => $validatedData['name'],
            'bio' => $validatedData['bio']
        ]);

        $authors = Author::all();

        $html = view('partials.authorsList', compact('authors'))->render();

        return response()->json(['success' => true, 'html' => $html, 'message' => 'Author added successfully']);
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

        $validatedData = $validator->validated();

        $author = Author::findOrFail($validatedData['author_id']);
        $author->name = $validatedData['name'];
        $author->bio = $validatedData['bio'];
        $author->save();
        $authors = Author::all();
        $html = view('partials.authorsList', compact('authors'))->render();
        return response()->json(['success' => true, 'html' => $html, 'message' => 'Genrecd updated successfully']);
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:authors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        $author = Author::findorFail($request->get('id'));

        if($author->books()->count() > 0){

            $authors = Author::all();

            $html = view('partials.authorsList', compact('authors'))->render();

            return response()->json([
                'success' => false,
                'html' => $html,
                'errors' => 'This author cannot be deleted because it is associated with one or more books.'
            ], 422);
        }

        $author->delete();

        $authors = Author::all();
        $html = view('partials.authorsList', compact('authors'))->render();

        return response()->json(['success' => true, 'html' => $html, 'message' => 'Author deleted successfully']);
    }

    public function suggestions(Request $request)
    {
        $searchTerm = $request->input('search_term');
        $results = Author::where('name', 'LIKE', "%{$searchTerm}%")->get();

        $html = view('partials.suggestionsList', compact('results'))->render();

        return response()->json(['html' => $html]);
    }
}
