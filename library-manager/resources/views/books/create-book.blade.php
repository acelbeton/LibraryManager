@extends('layouts.app')

@section('content')


    <div class="container mt-5">
        <h2>Books</h2>
        <button class="btn btn-primary" data-toggle="modal" data-target="#createBookModal">Create New Book</button>
    </div>
    <div id="searchbar">
        @include('partials.searchbar')
    </div>

    <div class="modal fade" id="createBookModal" tabindex="-1" role="dialog" aria-labelledby="createBookModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBookModalLabel">Create New Book</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="create-book-form" action="{{ route('books.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="author_id">Author</label>
                            <select name="author_id" id="author_id" class="form-control" required>
                                @foreach($authors as $author)
                                    <option value="{{ $author->id }}">{{ $author->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="genre_id">Genre</label>
                            <select name="genre_id" id="genre_id" class="form-control" required>
                                @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="publisher_id">Publisher</label>
                            <select name="publisher_id" id="publisher_id" class="form-control" required>
                                @foreach($publishers as $publisher)
                                    <option value="{{ $publisher->id }}">{{ $publisher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cover_image">Cover Image</label>
                            <input type="file" name="cover_image" id="cover_image" class="form-control-file">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="booksList">
        @include('partials.booksList')
    </div>

@endsection
