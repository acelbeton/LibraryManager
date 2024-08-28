<div class="container">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(count($books) == 0)
        <div class="search-result-text">
            <h3>There are no books found.</h3>
        </div>
    @else
        <table class="table table-bordered table-hover mt-4">
            <thead>
            <tr>
                <th>Language</th>
                <th>Title</th>
                <th>Description</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Publisher</th>
                <th>Keywords</th>
                <th>Cover Image</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($books as $book)
                <tr id="book-{{ $book->id }}"
                    data-original-title="{{ $book->title }}"
                    data-original-description="{{ $book->description }}"
                    data-original-keywords="{{ json_encode($book->keywords->pluck('keyword')->toArray()) }}">

                    <td>
                        <select class="form-control select-language" data-book-id="{{ $book->id }}">
                            <option value="default">Default</option>
                            @if(isset($languages))
                                @foreach($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->language_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                    <td class="book-title">{{ $book->title }}</td>
                    <td class="book-description">{{ $book->description }}</td>
                    <td>{{ $book->author->name }}</td>
                    <td class="book-genre">{{ $book->genre->name }}</td>
                    <td>{{ $book->publisher->name }}</td>
                    <td class="book-keywords">
                        @if($book->keywords->isNotEmpty())
                            @foreach($book->keywords as $keyword)
                                <span class="badge badge-secondary" style="color: #000;">{{ $keyword->keyword }}</span>
                            @endforeach
                        @else
                            <span>No Keywords</span>
                        @endif
                    </td>
                    <td>
                        @if ($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover Image" width="100" height="160">
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-warning update-book"
                                data-id="{{ $book->id }}"
                                data-title="{{ $book->title }}"
                                data-description="{{ $book->description }}"
                                data-author_id="{{ $book->author_id }}"
                                data-genre_id="{{ $book->genre_id }}"
                                data-publisher_id="{{ $book->publisher_id }}"
                                data-keywords="{{ json_encode($book->keywords->pluck('keyword')->toArray()) }}">
                            Update
                        </button>
                        <button class="btn btn-primary add-translation"
                                data-bs-toggle="modal"
                                data-bs-target="#addTranslationModal"
                                data-book_id="{{ $book->id }}">
                            Add Translation
                        </button>
                        <button class="btn btn-danger delete-book" data-id="{{ $book->id }}">Delete</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="updateBookModal" tabindex="-1" aria-labelledby="updateBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateBookModalLabel">Update Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-book-form" action="{{ route('books.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="update-book-id">
                    <div class="mb-3">
                        <label for="update-title" class="form-label">Title:</label>
                        <input type="text" name="title" id="update-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="update-description" class="form-label">Description:</label>
                        <textarea name="description" id="update-description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="update-author_id" class="form-label">Author:</label>
                        @if(isset($authors))
                            <select name="author_id" id="author_id" class="form-select" required>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="update-genre_id" class="form-label">Genre:</label>
                        @if(isset($genres))
                            <select name="genre_id" id="genre_id" class="form-select" required>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="update-publisher_id" class="form-label">Publisher:</label>
                        @if(isset($publishers))
                            <select name="publisher_id" id="publisher_id" class="form-select" required>
                                @foreach($publishers as $publisher)
                                    <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="update-keywords" class="form-label">Keywords</label>
                        <input type="text" name="keywords" id="update-keywords" class="form-control" placeholder="Enter keywords separated by commas">
                    </div>
                    <div class="mb-3">
                        <label for="update-cover_image" class="form-label">Cover Image</label>
                        <input type="file" name="cover_image" id="update-cover_image" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addTranslationModal" tabindex="-1" aria-labelledby="addTranslationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTranslationModalLabel">Add Translation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="translation-form" id="translate-book-form" action="{{ route('books.translate.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="book_id" id="translation-book-id">
                    <div class="mb-3">
                        <label for="translate-title" class="form-label">Title:</label>
                        <input type="text" name="translated_title" id="translate-title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="translated_description" class="form-label">Description:</label>
                        <textarea name="translated_description" id="translate-description" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="keywords" class="form-label">Keywords</label>
                        <input type="text" name="keywords" id="keywords" class="form-control" placeholder="Enter keywords separated by commas">
                    </div>
                    <div class="mb-3">
                        <select name="language_id" id="language_id" class="form-select" required>
                            @if(isset($languages))
                                @foreach($languages as $language)
                                    <option value="{{ $language->id }}">{{ $language->language_name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Translation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

