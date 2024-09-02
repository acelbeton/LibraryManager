<table class="table table-bordered table-hover">
    <thead>
    <tr>
        <th>Name</th>
        <th>Address</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($publishers as $publisher)
        <tr>
            <td>{{ $publisher->name }}</td>
            <td>{{ $publisher->address }}</td>
            <td>
                <button class="btn btn-warning update-publisher"
                        data-id="{{ $publisher->id }}"
                        data-name="{{ $publisher->name }}"
                        data-address="{{ $publisher->address }}"
                        data-bs-toggle="modal" data-bs-target="#updatePublisherModal">
                    Update
                </button>
            </td>
            <td>
                <button class="btn btn-danger delete-publisher" data-id="{{ $publisher->id }}">Delete</button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3">No Publisher found.</td>
        </tr>
    @endforelse
    </tbody>
</table>
