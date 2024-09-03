<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function index() {
        $languages = Language::all();
        return view('languages.language-index', compact('languages'));
    }


    public function store(Request $request) {
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

        $validatedData = $validator->validated();

        $language = Language::findOrFail($validatedData['language_id']);
        $language->name = $validatedData['language_name'];
        $language->language_code = $validatedData['language_code'];
        $language->save();
        $languages = Language::all();
        $html = view('partials.languagesList', compact('languages'))->render();
        return response()->json(['success' => true, 'html' => $html, 'message' => 'Language updated successfully']);
    }

    public function destroy(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:languages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
        }

        $language = Language::findorFail($request->get('id'));

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
    }
}
