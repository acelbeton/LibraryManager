@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Languages</h2>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createLanguageModal">
            Add New Language
        </button>

        <div id="languagesList">
            @include('partials.languagesList')
        </div>
    </div>

    <div class="modal fade" id="createLanguageModal" tabindex="-1" aria-labelledby="createLanguageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createLanguageModalLabel">Add New Language</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="create-language-form" action="{{ route('languages.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="language_name" class="form-label">Language</label>
                            <input type="text" name="language_name" id="language-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="language_code" class="form-label">Language Code</label>
                            <input type="text" name="language_code" id="language-code" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Language</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Author Modal -->
    <div class="modal fade" id="updateLanguageModal" tabindex="-1" aria-labelledby="updateLanguageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateLanguageModalLabel">Update Language</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-language-form" action="{{ route('languages.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="language_id" id="update-language-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="update-language-name" class="form-label">Language Name</label>
                            <input type="text" name="language_name" id="update-language-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="update-language-code" class="form-label">Language Code</label>
                            <input type="text" name="language_code" id="update-language-code" class="form-control" required>
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
