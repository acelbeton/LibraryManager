<?php


namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Genre;
use App\Models\GenreTranslation;
use App\Models\Keyword;
use App\Models\Publisher;
use App\Models\Translation;
use App\Traits\GetCachedData;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    use GetCachedData;

    public function index(Request $request) {
        try {
            $selectedLanguageId = $request->input('language_id');

            $books = Book::with(['author', 'genre', 'publisher', 'keywords' => function($query) use ($selectedLanguageId) {
                if ($selectedLanguageId) {
                    $query->where('language_id', $selectedLanguageId);
                }
            }])->get();

            if (!$selectedLanguageId) {
                foreach ($books as $book) {
                    $book->keywords = $book->keywords->where('language_id', $book->default_language_id);
                }
            }

            $cachedData = $this->getCachedData();

            if ($request->ajax() && $request->input('partial')) {
                $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();
                return response()->json(['html' => $html]);
            }

            return view('books.create-book', array_merge(['books' => $books, 'selectedLanguageId' => $selectedLanguageId], $cachedData));
        } catch (Exception $e) {
            Log::error('Error fetching books: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while retrieving books.');
        }
    }

    public function search(Request $request) {

        $validator = Validator::make($request->all(), [
           'search_term' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => 'Validation failed. Please check your input.'], 422);
        }

        try {


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
        } catch (QueryException $e) {
            Log::error('Database error during book search: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error during book search: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function getSuggestions(Request $request) {
        $validator = Validator::make($request->all(), [
            'search_term' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['html' => '']);
        }

        try {
            $searchTerm = $request->input('search_term');

            $results = $this->searchBookQuery($searchTerm)
                ->limit(5)
                ->get()
                ->filter(function ($result) {
                    return isset($result->title) || isset($result->name);
                });

            $html = view('partials.suggestionsList', compact('results'))->render();

            return response()->json(['html' => $html]);
        } catch (Exception $e) {
            Log::error('Unexpected error during suggestions: ' . $e->getMessage());
            return response()->json(['html' => '']);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'author_id' => 'nullable|exists:authors,id',
                'author_name' => 'nullable|string|max:255',
                'genre_id' => 'nullable|exists:genres,id',
                'genre_name' => 'nullable|string|max:255',
                'publisher_id' => 'nullable|exists:publishers,id',
                'publisher_name' => 'nullable|string|max:255',
                'default_language_id' => 'required|exists:languages,id',
                'cover_image' => 'nullable|image|max:2048',
                'keywords' => 'nullable|array',
                'keywords.*' => 'nullable|string|max:255',
            ]);

            $author = $this->getOrCreateAuthor($validatedData);
            $genre = $this->getOrCreateGenre($validatedData);
            $publisher = $this->getOrCreatePublisher($validatedData);

            if ($request->hasFile('cover_image')) {
                $validatedData['cover_image'] = $request->file('cover_image')->store('cover_images', 'public');
            }

            $book = Book::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'author_id' => $author->id,
                'genre_id' => $genre->id,
                'publisher_id' => $publisher->id,
                'default_language_id' => $validatedData['default_language_id'],
                'cover_image' => $validatedData['cover_image'] ?? null,
            ]);

            if(!empty($validatedData['keywords'])) {
                foreach ($validatedData['keywords'] as $keyword) {
                    $keywordRecord = Keyword::firstOrCreate([
                        'keyword' => $keyword,
                        'language_id' => $validatedData['default_language_id']
                    ]);

                    $book->keywords()->attach($keywordRecord->id);
                }
                $this->refreshCache('keywords', Keyword::class);
            }

            $books = Book::with(['author', 'genre', 'publisher', 'keywords'])->get();
            $cachedData = $this->getCachedData();
            $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

            return response()->json(['success' => true,'message' => 'Book added successfully!', 'html' => $html], 201);
        } catch (QueryException $e) {
            Log::error('Database error during book creation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error during book creation: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function update(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:books,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'author_id' => 'nullable|exists:authors,id',
                'author_name' => 'nullable|string|max:255',
                'genre_id' => 'nullable|exists:genres,id',
                'genre_name' => 'nullable|string|max:255',
                'publisher_id' => 'nullable|exists:publishers,id',
                'publisher_name' => 'nullable|string|max:255',
                'cover_image' => 'nullable|image|max:2048',
                'keywords' => 'nullable|array',
                'keywords.*' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => 'Validation failed. Please check your input.'], 422);
            }

            $validatedData = $validator->validated();

            $book = Book::findOrFail($validatedData['id']);

            $author = $this->getOrCreateAuthor($validatedData);
            $genre = $this->getOrCreateGenre($validatedData);
            $publisher = $this->getOrCreatePublisher($validatedData);

            if (!empty($validatedData['keywords'])) {
                $book->keywords()->detach();

                foreach ($validatedData['keywords'] as $keyword) {
                    $keywordRecord = Keyword::firstOrCreate([
                        'keyword' => $keyword,
                        'language_id' => $book->default_language_id
                    ]);

                    $book->keywords()->attach($keywordRecord->id);
                }
                $this->refreshCache('keywords', Keyword::class);
            }

            if ($request->hasFile('cover_image')) {
                $book->cover_image = $request->file('cover_image')->store('cover_images', 'public');
            }

            $book->title = $validatedData['title'];
            $book->description = $validatedData['description'];
            $book->author_id = $author->id;
            $book->genre_id = $genre->id;
            $book->publisher_id = $publisher->id;

            $book->save();

            $books = Book::with(['author', 'genre', 'publisher', 'keywords'])->get();
            $cachedData = $this->getCachedData();
            $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

            return response()->json(['success' => true, 'message' => 'Book updated successfully!', 'html' => $html]);
        } catch (ModelNotFoundException $e) {
            Log::error('Book not found: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Book not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error during book update: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error during book update: ' . $e->getMessage());
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

            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $book->delete();

            $books = Book::with(['author', 'genre', 'publisher', 'keywords'])->get();
            $cachedData = $this->getCachedData();
            $html = view('partials.booksList', array_merge(['books' => $books], $cachedData))->render();

            return response()->json(['success' => true,'message' => 'Book deleted successfully!', 'html' => $html]);
        } catch (ModelNotFoundException $e) {
            Log::error('Book not found for deletion: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'Book not found. Please check your input.'], 404);
        } catch (QueryException $e) {
            Log::error('Database error during book deletion: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error during book deletion: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    public function getTranslation(Book $book, $languageId) {
        try {
            $translation = Translation::where('book_id', $book->id)
                ->where('language_id', $languageId)
                ->first();

            $translatedKeywords = $book->keywords()
                ->where('language_id', $languageId)
                ->pluck('keyword')
                ->toArray();

            $translatedGenreName = GenreTranslation::where('genre_id', $book->genre_id)
                ->where('language_id', $languageId)
                ->value('translated_name');

            return response()->json([
                'success' => true,
                'translated_title' => $translation ? $translation->translated_title : null,
                'translated_description' => $translation ? $translation->translated_description : null,
                'translated_keywords' => $translatedKeywords,
                'translated_genre_name' => $translatedGenreName ?? $book->genre->name,
            ]);
        } catch (QueryException $e) {
            Log::error('Database error fetching translation for book: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'A database error occurred. Please try again later.'], 500);
        } catch (Exception $e) {
            Log::error('Unexpected error fetching translation for book: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'An unexpected error occurred. Please try again later.'], 500);
        }
    }

    private function searchBookQuery($searchTerm) {
        try {
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
        } catch (QueryException $e) {
            Log::error('Error searching for books: ' . $e->getMessage());
            return null;
        } catch (Exception $e) {
            Log::error('Unexpected error searching for books: ' . $e->getMessage());
            return null;
        }
    }

    private function getOrCreateAuthor($data)
    {
        return !empty($data['author_id']) ? Author::find($data['author_id']) : Author::firstOrCreate(['name' => $data['author_name']], ['bio' => 'No bio yet.']);
    }

    private function getOrCreateGenre($data)
    {
        return !empty($data['genre_id']) ? Genre::find($data['genre_id']) : Genre::firstOrCreate(['name' => $data['genre_name']]);
    }

    private function getOrCreatePublisher($data)
    {
        return !empty($data['publisher_id']) ? Publisher::find($data['publisher_id']) : Publisher::firstOrCreate(['name' => $data['publisher_name']], ['address' => 'No address yet.']);
    }
}
