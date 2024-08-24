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
                <th>Title</th>
                <th>Description</th>
                <th>Author</th>
                <th>Genre</th>
                <th>Publisher</th>
                <th>Cover Image</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
            </thead>
            <tbody>
            @foreach($books as $book)
                <tr>
                    <td>{{ $book->title }}</td>
                    <td>{{ $book->description }}</td>
                    <td>{{ $book->author->name }}</td>
                    <td>{{ $book->genre->name }}</td>
                    <td>{{ $book->publisher->name }}</td>
                    <td>
                        @if ($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="Cover Image" width="100" height="160">
                        @endif
                    </td>
                    <td>{{ $book->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $book->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <button class="btn btn-danger delete-book" data-id="{{ $book->id }}">Delete</button>
                    </td>
                    <td>
                        <button class="btn btn-warning update-book"
                                data-id="{{ $book->id }}"
                                data-title="{{ $book->title }}"
                                data-description="{{ $book->description }}"
                                data-author_id="{{ $book->author_id }}"
                                data-genre_id="{{ $book->genre_id }}"
                                data-publisher_id="{{ $book->publisher_id }}">
                            Update
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="modal fade" id="updateBookModal" tabindex="-1" role="dialog" aria-labelledby="updateBookModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateBookModalLabel">Update Book</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="update-book-form" action="{{ route('books.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="update-book-id">
                        <div class="form-group">
                            <label for="update-title">Title:</label>
                            <input type="text" name="title" id="update-title" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="update-description">Description:</label>
                            <textarea name="description" id="update-description" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="update-author_id">Author:</label>
                            @if(isset($authors))
                                <select name="author_id" id="author_id" class="form-control" required>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="update-genre_id">Genre:</label>
                            @if(isset($genres))
                                <select name="genre_id" id="genre_id" class="form-control" required>
                                    @foreach($genres as $genre)
                                        <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="update-publisher_id">Publisher:</label>
                            @if(isset($publishers))
                                <select name="publisher_id" id="publisher_id" class="form-control" required>
                                    @foreach($publishers as $publisher)
                                        <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="update-cover_image">Cover Image</label>
                            <input type="file" name="cover_image" id="update-cover_image" class="form-control-file">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Book</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
