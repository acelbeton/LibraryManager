@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center">Authors</h2>
        <div class="row mb-3 justify-content-center">
            <div class="col-md-4 mb-3">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#createAuthorModal">
                    Add New Author
                </button>
            </div>
        </div>
        <div id="authorsList">
            @include('partials.authorsList')
        </div>
    </div>

    <div class="modal fade" id="createAuthorModal" tabindex="-1" aria-labelledby="createAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAuthorModalLabel">Add New Author</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="create-author-form" action="{{ route('authors.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="author-name" class="form-label">Author Name</label>
                            <input type="text" name="name" id="author-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="author-bio" class="form-label">Author Bio</label>
                            <textarea name="bio" id="author-bio" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Author</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateAuthorModal" tabindex="-1" aria-labelledby="updateAuthorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateAuthorModalLabel">Update Author</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-author-form" action="{{ route('authors.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="author_id" id="update-author-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="update-author-name" class="form-label">Author Name</label>
                            <input type="text" name="name" id="update-author-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="update-author-bio" class="form-label">Author Bio</label>
                            <textarea name="bio" id="update-author-bio" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
