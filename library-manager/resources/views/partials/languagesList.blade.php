<table class="table-custom">
    <thead>
    <tr>
        <th>Language Name</th>
        <th>Language Code</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($languages as $language)
        <tr>
            <td>{{ $language->language_name }}</td>
            <td>{{ $language->language_code }}</td>
            <td>
                <button class="btn btn-warning update-language"
                        data-language_id="{{ $language->id }}"
                        data-language_name="{{ $language->language_name }}"
                        data-language_code="{{ $language->language_code }}"
                        data-bs-toggle="modal" data-bs-target="#updateLanguageModal">
                    Update
                </button>
                <button class="btn btn-danger delete-language" data-id="{{ $language->id }}">Delete</button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="3">No Languages found.</td>
        </tr>
    @endforelse
    </tbody>
</table>
