<?php

namespace App\Http\Controllers;

use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublisherController extends Controller
{
    public function index() {
        $publishers = Publisher::all();
        return view('publisher.publisher-index', compact('publishers'));
    }


    public function store(Request $request) {
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

        $validatedData = $validator->validated();

        $publisher = Publisher::findOrFail($validatedData['publisher_id']);
        $publisher->name = $validatedData['name'];
        $publisher->address = $validatedData['address'];
        $publisher->save();
        $publishers = Publisher::all();
        $html = view('partials.publishersList', compact('publishers'))->render();
        return response()->json(['success' => true, 'html' => $html, 'message' => 'Publisher updated successfully']);
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:publishers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        $publisher = Publisher::findorFail($request->get('id'));

        if($publisher->books()->count() > 0){

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
    }

    public function suggestions(Request $request)
    {
        $searchTerm = $request->input('search_term');
        $results = Publisher::where('name', 'LIKE', "%{$searchTerm}%")->get();

        $html = view('partials.suggestionsList', compact('results'))->render();

        return response()->json(['html' => $html]);
    }
}
