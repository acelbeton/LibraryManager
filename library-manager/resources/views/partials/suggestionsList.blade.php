<ul class="list-group">
    @forelse($books as $book)
        <li class="list-group-item suggestion-item" data-title="{{ $book->title }}">{{ $book->title }} by {{$book->author->name}}</li>
    @empty
        <li class="list-group-item">
            No suggestions found
        </li>
    @endforelse
</ul>
