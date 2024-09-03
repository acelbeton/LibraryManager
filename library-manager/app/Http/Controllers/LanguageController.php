<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;

class LanguageController extends Controller
{
    public function index() {
        try {
            $languages = Language::all();
            return view('languages.language-index', compact('languages'));
        } catch (Exception $e) {
            Log::error('Error fetching languages: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while retrieving languages.');
        }
    }

    public function store(Request $request) {
        try {
            $validatedData = $request->validate([
                'language_name' => 'required|max:255',
                'language_code' => 'nullable|string'
            ]);

            Language::create([
                'language_name' => $validatedData['language_name'],
                'language_code' => $validatedData['language_code']
            ]);

            $languages = Language::all();
            $html = view('partials.languagesList', compact('languages'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Language added successfully']);
        } catch (QueryException $e) {
            Log::error('Database error when adding language: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when adding language: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            'language_id' => 'required|exists:languages,id',
            'language_name' => 'required|string|max:255',
            'language_code' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        try {
            $validatedData = $validator->validated();

            $language = Language::findOrFail($validatedData['language_id']);
            $language->language_name = $validatedData['language_name'];
            $language->language_code = $validatedData['language_code'];
            $language->save();

            $languages = Language::all();
            $html = view('partials.languagesList', compact('languages'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Language updated successfully']);
        } catch (ModelNotFoundException $e) {
            Log::error('Language not found for updating: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Language not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error when updating language: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when updating language: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:languages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        try {
            $language = Language::findOrFail($request->get('id'));

            if ($language->books()->count() > 0 ||
                $language->translations()->count() > 0 ||
                $language->genreTranslations()->count() > 0 ||
                $language->keywords()->count() > 0) {

                $languages = Language::all();
                $html = view('partials.languagesList', compact('languages'))->render();

                return response()->json([
                    'success' => false,
                    'html' => $html,
                    'errors' => 'This language cannot be deleted because it is associated with one or more books, translations, genre translations, or keywords.'
                ], 422);
            }

            $language->delete();

            $languages = Language::all();
            $html = view('partials.languagesList', compact('languages'))->render();

            return response()->json(['success' => true, 'html' => $html, 'message' => 'Language deleted successfully']);
        } catch (ModelNotFoundException $e) {
            Log::error('Language not found for deletion: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'Language not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error when deleting language: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error when deleting language: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }
}
