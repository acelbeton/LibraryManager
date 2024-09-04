<nav class="navbar navbar-expand-lg">
    <a class="ajax-link navbar-brand" href="{{ url('/') }}">Home</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="{{ route('books.index') }}" class="ajax-link nav-link">Books</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('genres.index') }}" class="ajax-link nav-link">Genres</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('authors.index') }}" class="ajax-link nav-link">Authors</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('publishers.index') }}" class="ajax-link nav-link">Publishers</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('languages.index') }}" class="ajax-link nav-link">Languages</a>
            </li>
        </ul>
    </div>
</nav>
