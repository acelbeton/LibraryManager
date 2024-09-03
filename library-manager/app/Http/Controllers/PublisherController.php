<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{
    public function index() {
        try {
            $publishers = Publisher::all();
            return view('publisher.publisher-index', compact('publishers'));
        } catch (Exception $e) {
            Log::error('Error fetching publishers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while retrieving publishers.');
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'address' => 'required|max:255',
            ]);

            Publisher::create([
                'name' => $validatedData['name'],
                'address' => $validatedData['address'],
            ]);

            $publishers = Publisher::all();
            $html = view('partials.publishersList', compact('publishers'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Publisher added successfully']);
        } catch (QueryException $e) {
            Log::error('Database error when adding publisher: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when adding publisher: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'publisher_id' => 'required|exists:publishers,id',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        try {
            $validatedData = $validator->validated();

            $publisher = Publisher::findOrFail($validatedData['publisher_id']);
            $publisher->name = $validatedData['name'];
            $publisher->address = $validatedData['address'];
            $publisher->save();

            $publishers = Publisher::all();
            $html = view('partials.publishersList', compact('publishers'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Publisher updated successfully']);
        } catch (ModelNotFoundException $e) {
            Log::error('Publisher not found for updating: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Publisher not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error when updating publisher: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when updating publisher: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:publishers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        try {
            $publisher = Publisher::findOrFail($request->get('id'));

            if ($publisher->books()->count() > 0) {
                $publishers = Publisher::all();
                $html = view('partials.publishersList', compact('publishers'))->render();

                return response()->json([
                    'success' => false,
                    'html' => $html,
                    'errors' => 'This Publisher cannot be deleted because it is associated with one or more books.'
                ], 422);
            }

            $publisher->delete();

            $publishers = Publisher::all();
            $html = view('partials.publishersList', compact('publishers'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Publisher deleted successfully']);
        } catch (ModelNotFoundException $e) {
            Log::error('Publisher not found for deletion: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'Publisher not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error when deleting publisher: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when deleting publisher: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function suggestions(Request $request) {
        try {
            $searchTerm = $request->input('search_term');
            $results = Publisher::where('name', 'LIKE', "%{$searchTerm}%")->get();

            $html = view('partials.suggestionsList', compact('results'))->render();

            return response()->json(['html' => $html]);
        } catch (QueryException $e) {
            Log::error('Database error when fetching publisher suggestions: ' . $e->getMessage());
            return response()->json(['html' => '']);
        } catch (Exception $e) {
            Log::error('Unexpected error when fetching publisher suggestions: ' . $e->getMessage());
            return response()->json(['html' => '']);
        }
    }
}
