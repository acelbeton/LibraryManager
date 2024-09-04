<table class="table-custom">
    <thead>
    <tr>
        <th>Name</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($genres as $genre)
        <tr>
            <td>{{ $genre->name }}</td>
            <td>
                <button class="btn btn-primary add-genre-translation" data-id="{{ $genre->id }}" data-name="{{ $genre->name }}" data-bs-toggle="modal" data-bs-target="#addGenreTranslationModal">
                    Add Translations
                </button>
                <button class="btn btn-warning update-genre"
                        data-id="{{ $genre->id }}"
                        data-name="{{ $genre->name }}"
                        data-bs-toggle="modal" data-bs-target="#updateGenreModal">
                    Update
                </button>
                <button class="btn btn-danger delete-genre" data-id="{{ $genre->id }}">Delete</button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3">No genres found.</td>
        </tr>
    @endforelse
    </tbody>
</table>
