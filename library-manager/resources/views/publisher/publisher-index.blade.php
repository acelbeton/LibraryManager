@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2 class="text-center">Publishers</h2>
        <div class="row mb-3 justify-content-center">
            <div class="col-md-4 mb-3">
                <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#createPublisherModal">
                    Add New Publisher
                </button>
            </div>
        </div>

        <div id="publishersList">
            @include('partials.publishersList')
        </div>
    </div>

    <div class="modal fade" id="createPublisherModal" tabindex="-1" aria-labelledby="createPublisherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPublisherModalLabel">Add New Publisher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="create-publisher-form" action="{{ route('publishers.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="publisher-name" class="form-label">Publisher Name</label>
                            <input type="text" name="name" id="publisher-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="publisher-address" class="form-label">Publisher Address</label>
                            <input type="text" name="address" id="publisher-address" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Publisher</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updatePublisherModal" tabindex="-1" aria-labelledby="updatePublisherModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePublisherModalLabel">Update Publisher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="update-publisher-form" action="{{ route('publishers.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="publisher_id" id="update-publisher-id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="update-publisher-name" class="form-label">Publisher Name</label>
                            <input type="text" name="name" id="update-publisher-name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="update-publisher-address" class="form-label">Publisher Address</label>
                            <input type="text" name="address" id="update-publisher-address" class="form-control" required>
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
