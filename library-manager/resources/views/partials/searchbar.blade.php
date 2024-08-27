<div>
    <form id="search-book" action="{{ route('books.search') }}" method="POST" data-suggestions-url="{{ route('books.suggestions') }}">
        @csrf
        <div class="d-flex justify-content-between align-items-center">
            <div class="w-100">
                <input type="text" name="search_term" id="search_term" placeholder="Search Books" class="form-control w-100" autocomplete="off">
                <div id="suggestions" class="suggestions-list mt-1"></div>
            </div>
        </div>
        <div id="search-button" class="d-flex justify-content-center mt-3">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>
</div>
