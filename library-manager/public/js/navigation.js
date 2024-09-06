$(document).ready(function () {
    const englishLanguageId = "1";
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let currentGenreAjaxRequest = null;
    let currentBookAjaxRequest = null;
    let translationRequestPending = false;

    function initializeAll() {
        detachPreviousListeners();
        attachEventListeners();
    }

    function detachPreviousListeners() {
        $(document).off('.app');
    }

    function attachEventListeners() {
        setupFormSubmissions();
        setupDeletions();
        setupSuggestions();
        setupModals();
        setupLanguageSelectors();
        resetBookList();
    }

    function setupFormSubmissions() {
        const formsToHandle = [
            { formId: '#create-book-form', modalId: '#createBookModal', listId: '#booksList' },
            { formId: '#update-book-form', modalId: '#updateBookModal', listId: '#booksList' },
            { formId: '#translate-book-form', modalId: '#addTranslationModal', listId: '#booksList' },
            { formId: '#create-genre-form', modalId: '#createGenreModal', listId: '#genresList' },
            { formId: '#add-translation-form', modalId: '#addGenreTranslationModal', listId: '#genresList' },
            { formId: '#update-genre-form', modalId: '#updateGenreModal', listId: '#genresList' },
            { formId: '#create-author-form', modalId: '#createAuthorModal', listId: '#authorsList' },
            { formId: '#update-author-form', modalId: '#updateAuthorModal', listId: '#authorsList' },
            { formId: '#create-publisher-form', modalId: '#createPublisherModal', listId: '#publishersList' },
            { formId: '#update-publisher-form', modalId: '#updatePublisherModal', listId: '#publishersList' },
            { formId: '#create-language-form', modalId: '#createLanguageModal', listId: '#languagesList' },
            { formId: '#update-language-form', modalId: '#updateLanguageModal', listId: '#languagesList' }
        ];

        formsToHandle.forEach(({ formId, modalId, listId }) => {
            handleFormSubmission(formId, modalId, listId);
        });

        handleSearchSubmission('#search-book', '#booksList');
    }

    function setupDeletions() {
        const deletionsToHandle = [
            { selector: '.delete-book', url: '/books/destroy', listId: '#booksList', message: 'Are you sure you want to delete this book?' },
            { selector: '.delete-genre', url: '/genres/destroy', listId: '#genresList', message: 'Are you sure you want to delete this genre?' },
            { selector: '.delete-author', url: '/authors/destroy', listId: '#authorsList', message: 'Are you sure you want to delete this author?' },
            { selector: '.delete-publisher', url: '/publishers/destroy', listId: '#publishersList', message: 'Are you sure you want to delete this publisher?' },
            { selector: '.delete-language', url: '/languages/destroy', listId: '#languagesList', message: 'Are you sure you want to delete this language?' }
        ];

        deletionsToHandle.forEach(({ selector, url, listId, message }) => {
            handleDeletion(selector, url, listId, message);
        });
    }

    function setupSuggestions() {
        const suggestionsToHandle = [
            { inputId: '#search_term', url: '/books/suggestions', listId: '#suggestions' },
            { inputId: '#author-search', url: '/author/suggestions', hiddenInputId: '#author_id', listId: '#author-suggestions' },
            { inputId: '#genre-search', url: '/genre/suggestions', hiddenInputId: '#genre_id', listId: '#genre-suggestions' },
            { inputId: '#publisher-search', url: '/publisher/suggestions', hiddenInputId: '#publisher_id', listId: '#publisher-suggestions' },
            { inputId: '#update-author-search', url: '/author/suggestions', hiddenInputId: '#update-author_id', listId: '#update-author-suggestions' },
            { inputId: '#update-genre-search', url: '/genre/suggestions', hiddenInputId: '#update-genre_id', listId: '#update-genre-suggestions' },
            { inputId: '#update-publisher-search', url: '/publisher/suggestions', hiddenInputId: '#update-publisher_id', listId: '#update-publisher-suggestions' }
        ];

        suggestionsToHandle.forEach(({ inputId, url, hiddenInputId, listId }) => {
            handleSuggestions(inputId, url, hiddenInputId, listId);
        });
    }

    function setupModals() {
        handleUpdateGenre('.update-genre');
        handleUpdate('.update-book', 'book');
        handleUpdate('.update-author', 'author');
        handleUpdate('.update-publisher', 'publisher');
        handleUpdate('.update-language', 'language');

        $(document).on('click.app', '.add-translation', function () {
            const bookId = $(this).data('book_id');
            $('#translation-book-id').val(bookId);
            fetchBookTranslation(bookId, $('#language_id').val());
        });

        $(document).on('click.app', '.add-genre-translation', function () {
            const genreId = $(this).data('id');
            $('#translation-genre-id').val(genreId);
            $('#genre-original-name').val($(this).data('name'));
            fetchGenreTranslation(genreId, $('#language_id').val());
        });

        $(document).on('change.app', '#addTranslationModal #language_id', function () {
            fetchBookTranslation($('#translation-book-id').val(), $(this).val());
        });

        $(document).on('change.app', '#addGenreTranslationModal #language_id', function () {
            fetchGenreTranslation($('#translation-genre-id').val(), $(this).val());
        });
    }

    function setupLanguageSelectors() {
        let currentAjaxRequest = null;


        $(document).off('change.app', '.select-language').on('change.app', '.select-language', function () {
            const bookId = $(this).data('book-id');
            const languageId = $(this).val();


            if (currentAjaxRequest) {
                currentAjaxRequest.abort();
            }

            if (languageId === englishLanguageId) {
                resetBookDataToDefault(bookId);
            } else {
                currentAjaxRequest = fetchTranslatedBookData(bookId, languageId);
            }
        });
    }

    function fetchTranslatedBookData(bookId, languageId) {
        return $.ajax({
            url: `/books/${bookId}/translate/${languageId}`,
            method: 'GET',
            success: function (response) {
                let bookRow = $('#book-' + bookId);
                if (response.translated_title && response.translated_description) {
                    bookRow.find('.book-title').text(response.translated_title);
                    bookRow.find('.book-description').text(response.translated_description);
                }

                if (response.translated_genre_name) {
                    bookRow.find('.book-genre').text(response.translated_genre_name);
                }

                let keywordsContainer = bookRow.find('.book-keywords');
                keywordsContainer.empty();

                if (response.translated_keywords && response.translated_keywords.length > 0) {
                    response.translated_keywords.forEach(function (keyword) {
                        keywordsContainer.append('<span class="badge badge-secondary" style="color: #000;">' + keyword + '</span> ');
                    });
                } else {
                    keywordsContainer.append('<span>No Keywords</span>');
                }
            },
            error: function (xhr) {
                if (xhr.statusText !== 'abort') {
                    showToast('An error occurred while fetching the translation.', 'error');
                }
            }
        });
    }

    function fetchGenreTranslation(genreId, languageId) {
        if (translationRequestPending) return;
        translationRequestPending = true;

        if (currentGenreAjaxRequest) {
            currentGenreAjaxRequest.abort();
        }

        currentGenreAjaxRequest = $.ajax({
            url: `/genres/${genreId}/translations/${languageId}`,
            method: 'GET',
            success: function (response) {
                $('#translated_name').val(response.translation ? response.translation.translated_name : '');
            },
            error: function (xhr) {
                if (xhr.statusText !== 'abort') {
                    console.log('Error fetching genre translation:', xhr);
                }
            },
            complete: function () {
                translationRequestPending = false;
            }
        });
    }


    function fetchBookTranslation(bookId, languageId) {
        if (translationRequestPending) return;
        translationRequestPending = true;

        if (currentBookAjaxRequest) {
            currentBookAjaxRequest.abort();
        }

        currentBookAjaxRequest = $.ajax({
            url: `/books/${bookId}/translation/${languageId}`,
            method: 'GET',
            success: function (response) {
                $('#translate-title').val(response.translated_title || '');
                $('#translate-description').val(response.translated_description || '');
                $('#translate-keywords').val(response.translated_keywords ? response.translated_keywords.join(', ') : '');
            },
            error: function (xhr) {
                if (xhr.statusText !== 'abort') {
                    console.log('Error fetching book translation:', xhr);
                }
            },
            complete: function () {
                translationRequestPending = false;
            }
        });
    }

    $(document).off('change.app', '#language_id').on('change.app', '#language_id', function () {
        const bookId = $('#translation-book-id').val();
        const languageId = $(this).val();

        if (bookId) {
            fetchBookTranslation(bookId, languageId);
        } else {
            const genreId = $('#translation-genre-id').val();
            fetchGenreTranslation(genreId, languageId);
        }
    });

    function handleFormSubmission(formId, modalId, listId) {
        $(formId).off('submit').on('submit.app', function (event) {
            event.preventDefault();

            const $form = $(this);
            const formData = getFormDataWithKeywords($form);

            ajaxRequest($form.attr('action'), 'POST', formData, function (response) {
                if ($form.hasClass('translation-form') && response.exists) {
                    handleTranslationUpdate($form, formData, modalId, listId);
                } else {
                    updateListAndResetForm(response, modalId, listId, $form);
                }
            });
        });
    }

    function handleSearchSubmission(formId, listId) {
        $(formId).off('submit').on('submit.app', function (event) {
            event.preventDefault();
            const formData = new FormData($(this)[0]);

            ajaxRequest($(this).attr('action'), 'POST', formData, function (response) {
                $(listId).html(response.html);
                attachEventListeners();
            });
        });
    }

    function handleDeletion(buttonClass, deleteUrl, listId, confirmationMessage) {
        $(document).on('click', buttonClass, function () {
            const itemId = $(this).data('id');

            showBootstrapConfirmation(confirmationMessage, function () {
                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: itemId
                    },
                    success: function (response) {
                        $(listId).html(response.html);
                        showToast('Item deleted successfully.', 'success');
                    },
                    error: function (xhr) {
                        let errorMessage = 'There was an error';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            let errors = xhr.responseJSON.error;
                            if ($.isPlainObject(errors) || $.isArray(errors)) {
                                let errorString = '';
                                $.each(errors, function(key, value) {
                                    errorString += value[0] + '\n';
                                });
                                showToast(errorString, 'error');
                            } else {
                                showToast(errors, 'error');
                            }
                        } else {
                            showToast(errorMessage, 'error');
                        }
                    }
                });
            });
        });
    }


    function handleSuggestions(inputId, url, hiddenInputId = null, listId) {
        let typingTimer;
        const debounceDelay = 300;
        let currentAjaxRequest = null;

        $(document).off('keyup.app', inputId);
        $(document).off('click.app', `${listId} .suggestion-item`);
        $(document).off('focusout.app', inputId);

        $(document).on('keyup.app', inputId, function () {
            clearTimeout(typingTimer);

            const query = $(this).val().trim();
            if (query.length > 1) {
                typingTimer = setTimeout(() => {
                    if (currentAjaxRequest) {
                        currentAjaxRequest.abort();
                    }
                    currentAjaxRequest = fetchSuggestions(url, query, listId);
                }, debounceDelay);
            } else {
                $(listId).empty().hide();
            }
        });

        $(document).on('click.app', `${listId} .suggestion-item`, function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            if (hiddenInputId) {
                $(hiddenInputId).val(id);
            }
            $(inputId).val(name);

            $(listId).empty().hide();
        });

        $(document).on('focusout.app', inputId, function () {
            setTimeout(() => {
                $(listId).empty().hide();
            }, 200);
        });
    }

    function fetchSuggestions(url, query, listId) {
        return $.ajax({
            url: url,
            method: 'GET',
            data: { search_term: query },
            success: function (response) {
                if (response.html) {
                    $(listId).html(response.html).show();
                } else {
                    $(listId).empty().hide();
                }
            },
            error: function (xhr) {
                if (xhr.statusText !== 'abort') {
                    console.log('Error fetching suggestions:', xhr);
                }
            }
        });
    }

    function resetBookDataToDefault(bookId) {
        const bookRow = $(`#book-${bookId}`);
        const originalData = bookRow.data();
        updateBookUI(bookRow, originalData.originalTitle, originalData.originalDescription, originalData.originalGenre, originalData.originalKeywords);
    }

    function handleUpdate(buttonClass, type) {
        $(document).off('click.app', buttonClass).on('click.app', buttonClass, function () {
            const $button = $(this);
            if (type === 'author') {
                populateAuthorModal($button);
            } else if (type === 'publisher') {
                populatePublisherModal($button);
            } else if (type === 'language') {
                populateLanguageModal($button);
            } else {
                populateBookModal($button);
            }
        });
    }

    function handleUpdateGenre(buttonClass) {
        $(document).off('click.app', buttonClass).on('click.app', buttonClass, function () {
            $('#update-genre-id').val($(this).data('id'));
            $('#update-genre-name').val($(this).data('name'));
            $('#updateGenreModal').modal('show');
        });
    }

    function resetBookList() {
        $('#reset-books').on('click.app', function () {
            ajaxRequest($(this).data('url'), 'GET', { partial: true }, function (response) {
                $('#booksList').html(response.html);
                $('#search_term').val('');
                $('#suggestions').empty().hide();
            });
        });
    }


    function loadContent(url, pushState = true) {
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'html',
            success: function (response) {
                const newContent = $('<div>').append($.parseHTML(response)).find('#main-content').html();
                if (newContent) {
                    $('#main-content').html(newContent);
                    if (pushState) {
                        window.history.pushState({ path: url }, '', url);
                    }
                    initializeAll();
                } else {
                    showToast('HTML content not found in the response.', 'error');
                }
            },
            error: function (xhr) {
                console.log('Error occurred:', xhr);
                showToast('An error occurred while loading the page.', 'error');
            }
        });
    }

    function handleEvent(event) {
        event.preventDefault();
        const $element = $(this);
        const url = $element.is('a.ajax-link') ? $element.attr('href') : $element.attr('action');
        loadContent(url);
    }

    $('body').on('click', 'a.ajax-link', handleEvent)
        .on('submit', 'form.ajax-form', handleEvent);

    window.onpopstate = function (event) {
        if (event.state && event.state.path) {
            loadContent(event.state.path, false);
        }
    };

    // Utility

    function ajaxRequest(url, method, data, successCallback, errorCallback) {
        $.ajax({
            url,
            method,
            data,
            processData: method !== 'POST' || false,
            contentType: method !== 'POST' || false,
            success: successCallback,
            error: errorCallback || function (xhr) {
                showToast('An error occurred.', 'error');
            }
        });
    }

    function getFormDataWithKeywords($form) {
        $form.find('input[name="keywords[]"]').remove();
        const keywordsInput = $form.find('input[name="keywords"]');
        if (keywordsInput.length > 0) {
            const keywordsArray = keywordsInput.val().split(',').map(keyword => keyword.trim());
            keywordsArray.forEach(keyword => {
                if (keyword) $form.append(`<input type="hidden" name="keywords[]" value="${keyword}">`);
            });
        }
        return new FormData($form[0]);
    }

    function updateBookUI(bookRow, title, description, genre, keywords) {
        bookRow.find('.book-title').text(title);
        bookRow.find('.book-description').text(description);
        bookRow.find('.book-genre').text(genre);

        const keywordsContainer = bookRow.find('.book-keywords');
        keywordsContainer.empty();
        if (keywords && keywords.length > 0) {
            keywords.forEach(keyword => {
                keywordsContainer.append(`<span class="badge badge-secondary" style="color: #000;">${keyword}</span> `);
            });
        } else {
            keywordsContainer.append('<span>No Keywords</span>');
        }
    }

    function handleTranslationUpdate($form, formData, modalId, listId) {
        $(modalId).modal('hide');
        showBootstrapConfirmation('Translation exists. Do you want to update it?', function () {
            formData.append('update_existing', true);
            ajaxRequest($form.attr('action'), 'POST', formData, function (response) {
                updateListAndResetForm(response, modalId, listId, $form);
            });
        });
    }

    function updateListAndResetForm(response, modalId, listId, $form) {
        $(listId).html(response.html);
        $(modalId).modal('hide');
        $('.modal-backdrop').remove();
        $form[0].reset();
        $('#message').empty();
        attachEventListeners();
        showToast(response.message, 'success');
    }

    function populateAuthorModal($button) {
        $('#update-author-id').val($button.data('id'));
        $('#update-author-name').val($button.data('name'));
        $('#update-author-bio').val($button.data('bio'));
        $('#updateAuthorModal').modal('show');
    }

    function populatePublisherModal($button) {
        $('#update-publisher-id').val($button.data('id'));
        $('#update-publisher-name').val($button.data('name'));
        $('#update-publisher-address').val($button.data('address'));
        $('#updatePublisherModal').modal('show');
    }

    function populateLanguageModal($button) {
        $('#update-language-id').val($button.data('language_id'));
        $('#update-language-name').val($button.data('language_name'));
        $('#update-language-code').val($button.data('language_code'));
        $('#updateLanguageModal').modal('show');
    }

    function populateBookModal($button) {
        const bookId = $button.data('id');
        const title = $button.data('title');
        const description = $button.data('description');
        const authorId = $button.data('author_id');
        const genreId = $button.data('genre_id');
        const publisherId = $button.data('publisher_id');
        const keywords = $button.data('keywords');

        $('#update-book-id').val(bookId);
        $('#update-title').val(title);
        $('#update-description').val(description);
        $('#update-author-search').val($button.closest('tr').find('.book-author').text());
        $('#update-author_id').val(authorId);
        $('#update-genre-search').val($button.closest('tr').find('.book-genre').text());
        $('#update-genre_id').val(genreId);
        $('#update-publisher-search').val($button.closest('tr').find('.book-publisher').text());
        $('#update-publisher_id').val(publisherId);
        $('#update-keywords').val(keywords && Array.isArray(keywords) ? keywords.join(', ') : '');
        $('#updateBookModal').modal('show');
    }

    initializeAll();
});
