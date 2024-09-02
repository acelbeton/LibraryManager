@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Genres</h2>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createGenreModal">
            Add New Genre
        </button>

        <div id="genresList">
            @include('partials.genresList')
        </div>
    </div>

    <!-- Create Genre Modal -->
    <div class="modal fade" id="createGenreModal" tabindex="-1" aria-labelledby="createGenreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createGenreModalLabel">Add New Genre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="create-genre-form" action="{{ route('genres.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="genre-name" class="form-label">Genre Name</label>
                            <input type="text" name="name" id="genre-name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Genre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add or Update Genre Translation Modal -->
    <div class="modal fade" id="addGenreTranslationModal" tabindex="-1" aria-labelledby="addGenreTranslationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addGenreTranslationModalLabel">Add or Update Translation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-translation-form" action="{{ route('genres.translations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="genre_id" id="translation-genre-id">
                    <input type="hidden" name="translation_id" id="translation-id"> <!-- To track the translation if updating -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="genre-original-name" class="form-label">Original Genre Name</label>
                            <input type="text" id="genre-original-name" class="form-control" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="language_id" class="form-label">Language</label>
                            <select name="language_id" id="language_id" class="form-select" required>
                                @foreach($languages as $language)
                                    @if($language->language_name !== 'English')
                                        <option value="{{ $language->id }}">{{ $language->language_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="translated_name" class="form-label">Translated Name</label>
                            <input type="text" name="translated_name" id="translated_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Translation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Genre Modal -->
    <div class="modal fade" id="updateGenreModal" tabindex="-1" aria-labelledby="updateGenreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateGenreModalLabel">Update Genre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-genre-form" action="{{ route('genres.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="genre_id" id="update-genre-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="update-genre-name" class="form-label">Genre Name</label>
                            <input type="text" name="name" id="update-genre-name" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
