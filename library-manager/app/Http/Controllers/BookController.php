<?php


namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;
use App\Models\Keyword;
use App\Models\Language;
use App\Models\Publisher;
use App\Models\Translation;
use App\Traits\GetCachedData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    use GetCachedData;

    public function index() {
        $books = Book::with(['author', 'genre', 'publisher', 'keywords'])->get();
        $cachedData = $this->getCachedData();
        return view('books.create-book', array_merge(['books' => $books], $cachedData));
    }

    public function search(Request $request) {

        $validator = Validator::make($request->all(), [
           'search_term' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => 'Validation failed. Please check your input.'], 422);
        }

        $searchTerm = $request->input('search_term');

        $books = $this->searchBookQuery($searchTerm)
            ->with(['author', 'genre', 'publisher', 'keywords'])
            ->paginate(10);

        $cachedData = $this->getCachedData();

        $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

        if ($books->isEmpty()) {
            return response()->json(['success' => false, 'html' => $html, 'errors' => 'No results found.']);
        }

        return response()->json(['success' => true, 'html' => $html]);
    }

    public function getSuggestions(Request $request) {
        $validator = Validator::make($request->all(), [
            'search_term' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['html' => '']);
        }

        $searchTerm = $request->input('search_term');

        $books = $this->searchBookQuery($searchTerm)->limit(5)->get();

        $html = view('partials.suggestionsList', compact('books'))->render();

        return response()->json(['html' => $html]);
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'author_id' => 'required|exists:authors,id',
                'genre_id' => 'required|exists:genres,id',
                'publisher_id' => 'required|exists:publishers,id',
                'default_language_id' => 'required|exists:languages,id',
                'cover_image' => 'nullable|image|max:2048',
                'keywords' => 'nullable|array',
                'keywords.*' => 'nullable|string|max:255',
            ]);

            if ($request->hasFile('cover_image')) {
                $validatedData['cover_image'] = $request->file('cover_image')->store('cover_images', 'public');
            }

            $book = Book::create($validatedData);

            if(!empty($validatedData['keywords'])) {
                foreach ($validatedData['keywords'] as $keyword) {
                    $keywordRecord = Keyword::firstOrCreate([
                        'keyword' => $keyword,
                        'language_id' => $validatedData['default_language_id']
                    ]);

                    $book->keywords()->attach($keywordRecord->id);
                }
            }

            $books = Book::with(['author', 'genre', 'publisher', 'keywords'])->get();
            $cachedData = $this->getCachedData();
            $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

            return response()->json(['success' => true,'message' => 'Book added successfully!', 'html' => $html], 201);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function update(Request $request) {

        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:books,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'author_id' => 'required|exists:authors,id',
                'genre_id' => 'required|exists:genres,id',
                'publisher_id' => 'required|exists:publishers,id',
                'cover_image' => 'nullable|image|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
            }

            $book = Book::findOrFail($request->id);

            $book->title = $request->title;
            $book->description = $request->description;
            $book->author_id = $request->author_id;
            $book->genre_id = $request->genre_id;
            $book->publisher_id = $request->publisher_id;

            if ($request->hasFile('cover_image')) {
                $book->cover_image = $request->file('cover_image')->store('cover_images', 'public');
            }

            $book->save();

            $books = Book::with(['author', 'genre', 'publisher', 'keywords'])->get();
            $cachedData = $this->getCachedData();
            $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

            return response()->json(['success' => true, 'message' => 'Book updated successfully!', 'html' => $html]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function destroy(Request $request){

        try {
            $validator = Validator::make($request->all(), ['id' => 'required|exists:books,id']);

            if($validator->fails()) {
                return response()->json(['success' => false, 'errors' => 'There was an error with the deletion'], 422);
            }

            $book = Book::findOrFail($request->id);
            $book->delete();

            $books = Book::with(['author', 'genre', 'publisher', 'keywords'])->get();
            $cachedData = $this->getCachedData();
            $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

            return response()->json(['success' => true,'message' => 'Book deleted successfully!', 'html' => $html]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return response()->json(['success' => false, 'errors' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }

    public function getTranslation(Book $book, $languageId) {
        $translation = Translation::where('book_id', $book->id)
            ->where('language_id', $languageId)
            ->first();

        $translatedKeywords = $book->keywords()
            ->where('language_id', $languageId)
            ->pluck('keyword')
            ->toArray();

        if ($translation) {
            return response()->json([
                'success' => true,
                'translated_title' => $translation->translated_title,
                'translated_description' => $translation->translated_description,
                'translated_keywords' => $translatedKeywords
            ]);
        } else {
            return response()->json([
                'translated_title' => null,
                'translated_description' => null,
                'translated_keywords' => $translatedKeywords
            ]);
        }
    }

    private function searchBookQuery($searchTerm) {
        return Book::where('title', 'LIKE', '%' . $searchTerm . '%')
            ->orWhereHas('author', function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');
            })
            ->orWhereHas('genre', function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');
            })
            ->orWhereHas('publisher', function ($query) use ($searchTerm) {
                $query->where('name', 'LIKE', '%' . $searchTerm . '%');
            })
            ->orWhereHas('keywords', function ($query) use ($searchTerm) {
                $query->where('keyword', 'LIKE', '%' . $searchTerm . '%');
            });
    }
}
