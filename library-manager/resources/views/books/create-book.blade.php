@extends('layouts.app')

@section('content')

    <div class="container mt-5">
        <h2>Books</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createBookModal">Create New Book</button>
    </div>
    <div id="searchbar">
        @include('partials.searchbar')
    </div>

    <div class="modal fade" id="createBookModal" tabindex="-1" aria-labelledby="createBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBookModalLabel">Create New Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="create-book-form" action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="author-search" class="form-label">Author</label>
                            <input type="text" id="author-search" class="form-control" placeholder="Search author" required>
                            <input type="hidden" name="author_id" id="author_id">
                            <div id="author-suggestions" class="suggestions-list"></div>
                        </div>

                        <div class="mb-3">
                            <label for="genre-search" class="form-label">Genre</label>
                            <input type="text" id="genre-search" class="form-control" placeholder="Search genre" required>
                            <input type="hidden" name="genre_id" id="genre_id">
                            <div id="genre-suggestions" class="suggestions-list"></div>
                        </div>

                        <div class="mb-3">
                            <label for="publisher-search" class="form-label">Publisher</label>
                            <input type="text" id="publisher-search" class="form-control" placeholder="Search publisher" required>
                            <input type="hidden" name="publisher_id" id="publisher_id">
                            <div id="publisher-suggestions" class="suggestions-list"></div>
                        </div>

                        <div class="mb-3">
                            <label for="keywords" class="form-label">Keywords</label>
                            <input type="text" name="keywords" id="keywords" class="form-control" placeholder="Enter keywords separated by commas">
                        </div>

                        <input type="hidden" name="default_language_id" value="1">

                        <div class="mb-3">
                            <label for="cover_image" class="form-label">Cover Image</label>
                            <input type="file" name="cover_image" id="cover_image" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
