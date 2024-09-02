<ul class="list-group">
    @forelse($results as $result)
        <li class="list-group-item suggestion-item"
            data-id="{{ $result->id ?? '' }}"
            data-name="{{ $result->name ?? $result->title ?? 'Unnamed' }}">
            {{ $result->name ?? $result->title ?? 'Unnamed' }}
        </li>
    @empty
        <li class="list-group-item">
            No suggestions found
        </li>
    @endforelse
</ul>
