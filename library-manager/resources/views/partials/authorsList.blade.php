<table class="table-custom">
    <thead>
    <tr>
        <th>Name</th>
        <th>Bio</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($authors as $author)
        <tr>
            <td>{{ $author->name }}</td>
            <td>{{ $author->bio }}</td>
            <td>
                <button class="btn btn-warning update-author"
                        data-id="{{ $author->id }}"
                        data-name="{{ $author->name }}"
                        data-bio="{{ $author->bio }}"
                        data-bs-toggle="modal" data-bs-target="#updateAuthorModal">
                    Update
                </button>
                <button class="btn btn-danger delete-author" data-id="{{ $author->id }}">Delete</button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3">No Authors found.</td>
        </tr>
    @endforelse
    </tbody>
</table>
