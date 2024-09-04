$(document).ready(function() {
    const englishLanguageId = "1";

    function initializeAll() {
        detachPreviousListeners();
        attachEventListeners()
    }

    function detachPreviousListeners() {
        $(document).off();
    }

    function attachEventListeners() {
        setupFormSubmissions();
        setupDeletions();
        setupSuggestions();
        setupModals();
        setupLanguageSelectors();
    }

    function setupFormSubmissions() {
        handleFormSubmission('#create-book-form', '#createBookModal', '#booksList');
        handleFormSubmission('#update-book-form', '#updateBookModal', '#booksList');
        handleFormSubmission('#translate-book-form', '#addTranslationModal', '#booksList');
        handleFormSubmission('#create-genre-form', '#createGenreModal', '#genresList');
        handleFormSubmission('#add-translation-form', '#addGenreTranslationModal', '#genresList');
        handleFormSubmission('#update-genre-form', '#updateGenreModal', '#genresList');
        handleFormSubmission('#create-author-form','#createAuthorModal', '#authorsList');
        handleFormSubmission('#update-author-form', '#updateAuthorModal', '#authorsList');
        handleFormSubmission('#create-publisher-form','#createPublisherModal', '#publishersList');
        handleFormSubmission('#update-publisher-form', '#updatePublisherModal', '#publishersList');
        handleFormSubmission('#create-language-form','#createLanguageModal', '#languagesList');
        handleFormSubmission('#update-language-form', '#updateLanguageModal', '#languagesList');
        handleSearchSubmission('#search-book', '#booksList');
    }

    function setupDeletions() {
        handleDeletion('.delete-book', '/books/destroy', '#booksList', 'Are you sure you want to delete this book?');
        handleDeletion('.delete-genre', '/genres/destroy', '#genresList', 'Are you sure you want to delete this genre?');
        handleDeletion('.delete-author', '/authors/destroy', '#authorsList', 'Are you sure you want to delete this author?');
        handleDeletion('.delete-publisher', '/publishers/destroy', '#publishersList', 'Are you sure you want to delete this Publisher?');
        handleDeletion('.delete-language', '/languages/destroy', '#languagesList', 'Are you sure you want to delete this Language?');
    }

    function setupSuggestions() {
        handleSuggestions('#search_term', '/books/suggestions', null, '#suggestions');
        handleSuggestions('#author-search', '/author/suggestions', '#author_id', '#author-suggestions');
        handleSuggestions('#genre-search', '/genre/suggestions', '#genre_id', '#genre-suggestions');
        handleSuggestions('#publisher-search', '/publisher/suggestions', '#publisher_id', '#publisher-suggestions');
        handleSuggestions('#update-author-search', '/author/suggestions', '#update-author_id', '#update-author-suggestions');
        handleSuggestions('#update-genre-search', '/genre/suggestions', '#update-genre_id', '#update-genre-suggestions');
        handleSuggestions('#update-publisher-search', '/publisher/suggestions', '#update-publisher_id', '#update-publisher-suggestions');
    }

    function setupModals() {
        handleUpdateGenre('.update-genre');
        handleUpdate('.update-book', 'book');
        handleUpdate('.update-author', 'author');
        handleUpdate('.update-publisher', 'publisher');
        handleUpdate('.update-language', 'language');

        $('.add-translation').on('click', function() {
            let bookId = $(this).data('book_id');
            $('#translation-book-id').val(bookId);

            let languageId = $('#language_id').val();
            fetchBookTranslation(bookId, languageId);
        });

        $('.add-genre-translation').on('click', function() {
            let genreId = $(this).data('id');
            let genreName = $(this).data('name');
            $('#translation-genre-id').val(genreId);
            $('#genre-original-name').val(genreName);

            let languageId = $('#language_id').val();
            fetchGenreTranslation(genreId, languageId);
        });

        $('#addTranslationModal #language_id').on('change', function() {
            let bookId = $('#translation-book-id').val();
            let languageId = $(this).val();
            fetchBookTranslation(bookId, languageId);
        });

        $('#addGenreTranslationModal #language_id').on('change', function() {
            let genreId = $('#translation-genre-id').val();
            let languageId = $(this).val();
            fetchGenreTranslation(genreId, languageId);
        });
    }

    function setupLanguageSelectors() {
        $(document).on('change', '.select-language', function() {
            let bookId = $(this).data('book-id');
            let languageId = $(this).val();

            console.log('Language selected:', languageId, 'Book ID:', bookId);

            if (languageId === englishLanguageId) {
                console.log('Resetting to default language');
                resetBookDataToDefault(bookId);
            } else {
                fetchTranslatedBookData(bookId, languageId);
            }
        });
    }

    function fetchGenreTranslation(genreId, languageId) {
        $.ajax({
            url: `/genres/${genreId}/translations/${languageId}`,
            method: 'GET',
            success: function(response) {
                if (response.translation) {
                    $('#translated_name').val(response.translation.translated_name);
                } else {
                    $('#translated_name').val('');
                }
            },
            error: function(xhr) {
                showToast('Error fetching genre translation.', 'error');
                $('#translated_name').val('');
            }
        });
    }

    function fetchBookTranslation(bookId, languageId) {
        $.ajax({
            url: `/books/${bookId}/translation/${languageId}`,
            method: 'GET',
            success: function(response) {
                $('#translate-title').val(response.translated_title || '');
                $('#translate-description').val(response.translated_description || '');
                if (response.translated_keywords && response.translated_keywords.length > 0) {
                    const keywordsString = response.translated_keywords.join(', ');
                    $('#translate-keywords').val(keywordsString);
                } else {
                    $('#translate-keywords').val('');
                }
            },
            error: function(xhr) {
                showToast('Error fetching book translation.', 'error');
                $('#translate-title').val('');
                $('#translate-description').val('');
                $('#keywords').val('');
            }
        });
    }

    function handleFormSubmission(formId, modalId, listId) {
        $(formId).off('submit');

        $(formId).on('submit', function(event) {
            event.preventDefault();

            const $form = $(this);

            $form.find('input[name="keywords[]"]').remove();

            const keywordsInput = $form.find('input[name="keywords"]');
            if (keywordsInput.length > 0) {
                const keywordsString = keywordsInput.val();
                const keywordsArray = keywordsString.split(',').map(function(keyword) {
                    return keyword.trim();
                });

                keywordsArray.forEach(function(keyword) {
                    if (keyword) {
                        $form.append(`<input type="hidden" name="keywords[]" value="${keyword}">`);
                    }
                });
            }

            const formData = new FormData($form[0]);
            const isTranslationForm = $form.hasClass('translation-form');

            $.ajax({
                url: $form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (isTranslationForm && response.exists) {
                        $(modalId).modal('hide');

                        showBootstrapConfirmation(response.message, function() {
                            formData.append('update_existing', true);
                            $.ajax({
                                url: $form.attr('action'),
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(updateResponse) {
                                    $(listId).html(updateResponse.html);
                                    $(modalId).modal('hide');
                                    $('.modal-backdrop').remove();
                                    $form[0].reset();
                                    $('#message').empty();
                                    attachEventListeners();
                                    showToast('Translation updated successfully.', 'success');
                                },
                                error: function(xhr) {
                                    let errorMessage = 'There was an error updating the translation';
                                    if (xhr.responseJSON && xhr.responseJSON.error) {
                                        errorMessage = xhr.responseJSON.error;
                                    }
                                    showToast(errorMessage, 'error');
                                }
                            });
                        });
                    } else if (response.html) {
                        $(listId).html(response.html);
                        $(modalId).modal('hide');
                        $('.modal-backdrop').remove();
                        $form[0].reset();
                        $('#message').empty();
                        attachEventListeners();
                        showToast(response.message, 'success');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'There was an error';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    showToast(errorMessage, 'error');
                }
            });
        });
    }

    function handleSearchSubmission(formId, listId) {
        $(formId).off('submit');

        $(formId).on('submit', function(event) {
           event.preventDefault();

           const $form = $(this);
           const formData = new FormData($form[0]);

           $.ajax({
              url: $form.attr('action'),
              method: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              success: function(response) {
                  if (response.html) {
                      $(listId).html(response.html);
                      attachEventListeners();
                  }
              },
              error: function(xhr) {
                  let errorMessage = 'No books found';
                  if (xhr.responseJSON && xhr.responseJSON.error) {
                      errorMessage = xhr.responseJSON.error;
                  }
                  showToast(errorMessage, 'error');
              }
           });
        });
    }

    function handleDeletion(buttonClass, deleteUrl, listId, confirmationMessage) {
        $(document).off('click', buttonClass);

        $(document).on('click', buttonClass, function() {
            let $button = $(this);
            let itemId = $button.data('id');

            showBootstrapConfirmation(confirmationMessage, function() {
                $button.prop('disabled', true);

                $.ajax({
                    url: deleteUrl,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: itemId
                    },
                    success: function(response) {
                        if (response.html) {
                            $(listId).html(response.html);
                            attachEventListeners();
                            showToast('Item deleted successfully.', 'success');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'There was an error';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;

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
                        $button.prop('disabled', false);
                    }
                });
            });
        });
    }


    function handleSuggestions(inputId, suggestionsUrl, hiddenInputId = null, suggestionsListId) {
        let typingTimer;
        const debounceDelay = 300;
        let currentRequest = null;
        let disableKeyup = false;

        function fetchSuggestions(query) {
            if (currentRequest) {
                currentRequest.abort();
            }

            currentRequest = $.ajax({
                url: suggestionsUrl,
                method: 'GET',
                data: { search_term: query },
                success: function (response) {
                    if (response.html) {
                        $(suggestionsListId).html(response.html).show();
                    } else {
                        $(suggestionsListId).empty().hide();
                    }
                },
                error: function (xhr) {
                    if (xhr.statusText !== "abort") {
                        showToast('Error fetching suggestions.', 'error');
                    }
                    $(suggestionsListId).empty().hide();
                },
                complete: function () {
                    currentRequest = null;
                }
            });
        }

        $(document).on('keyup', inputId, function () {
            clearTimeout(typingTimer);

            if (disableKeyup) {
                return;
            }

            let query = $(this).val().trim();

            if (query.length > 1) {
                typingTimer = setTimeout(function () {
                    fetchSuggestions(query);
                }, debounceDelay);
            } else {
                $(suggestionsListId).empty().hide();
            }
        });

        $(document).on('click', `${suggestionsListId} .suggestion-item`, function () {
            const id = $(this).data('id');
            const name = $(this).data('name');

            if (hiddenInputId) {
                $(hiddenInputId).val(id);
            }
            $(inputId).val(name);

            disableKeyup = true;
            setTimeout(() => disableKeyup = false, 200);

            $(suggestionsListId).empty().hide();
        });

        $(inputId).on('focusout', function () {
            setTimeout(function () {
                $(suggestionsListId).empty().hide();
            }, 200);
        });
    }



    function resetBookDataToDefault(bookId) {
        let bookRow = $('#book-' + bookId);
        let originalTitle = bookRow.data('original-title');
        let originalDescription = bookRow.data('original-description');
        let originalKeywords = bookRow.data('original-keywords');
        let originalGenre = bookRow.data('original-genre');


        bookRow.find('.book-title').text(originalTitle);
        bookRow.find('.book-description').text(originalDescription);
        bookRow.find('.book-genre').text(originalGenre);

        let keywordsContainer = bookRow.find('.book-keywords');
        keywordsContainer.empty();

        if (originalKeywords && originalKeywords.length > 0) {
            originalKeywords.forEach(function(keyword) {
                let keywordBadge = $('<span>').addClass('badge badge-secondary').css('color', '#000').text(keyword);
                keywordsContainer.append(keywordBadge);
            });
        } else {
            keywordsContainer.append('<span>No Keywords</span>');
        }
    }

    function fetchTranslatedBookData(bookId, languageId) {
        $.ajax({
            url: '/books/' + bookId + '/translate/' + languageId,
            method: 'GET',
            success: function(response) {
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
                    response.translated_keywords.forEach(function(keyword) {
                        keywordsContainer.append('<span class="badge badge-secondary" style="color: #000;">' + keyword + '</span> ');
                    });
                } else {
                    keywordsContainer.append('<span>No Keywords</span>');
                }
            },
            error: function(xhr) {
                showToast('An error occurred while fetching the translation.', 'error');
            }
        });
    }


    function handleUpdate(buttonClass, type = 'book') {
        $(document).off('click', buttonClass);

        $(document).on('click', buttonClass, function() {
            let $button = $(this);

            if (type === 'author') {
                let authorId = $button.data('id');
                let name = $button.data('name');
                let bio = $button.data('bio');

                $('#update-author-id').val(authorId);
                $('#update-author-name').val(name);
                $('#update-author-bio').val(bio);

                $('#updateAuthorModal').modal('show');
            } else if (type === 'publisher') {
                let publisherId = $button.data('id');
                let name = $button.data('name');
                let address = $button.data('address');

                $('#update-publisher-id').val(publisherId);
                $('#update-publisher-name').val(name);
                $('#update-publisher-address').val(address);

                $('#updatePublisherModal').modal('show');
            } else if (type === 'language') {
                let languageId = $button.data('language_id');
                let name = $button.data('language_name');
                let code = $button.data('language_code');

                $('#update-language-id').val(languageId);
                $('#update-language-name').val(name);
                $('#update-language-code').val(code);
            } else {
                let bookId = $button.data('id');
                let title = $button.data('title');
                let description = $button.data('description');
                let authorId = $button.data('author_id');
                let genreId = $button.data('genre_id');
                let publisherId = $button.data('publisher_id');
                let keywords = $button.data('keywords');

                $('#update-book-id').val(bookId);
                $('#update-title').val(title);
                $('#update-description').val(description);

                $('#update-author-search').val($button.closest('tr').find('.book-author').text());
                $('#update-author_id').val(authorId);

                $('#update-genre-search').val($button.closest('tr').find('.book-genre').text());
                $('#update-genre_id').val(genreId);

                $('#update-publisher-search').val($button.closest('tr').find('.book-publisher').text());
                $('#update-publisher_id').val(publisherId);

                if (keywords && Array.isArray(keywords)) {
                    $('#update-keywords').val(keywords.join(', '));
                } else {
                    $('#update-keywords').val('');
                }

                $('#updateBookModal').modal('show');
            }
        });
    }


    function handleUpdateGenre(buttonClass) {
        $(document).off('click', buttonClass);

        $(document).on('click', buttonClass, function() {
            let $button = $(this);
            let genreId = $button.data('id');
            let name = $button.data('name');

            $('#update-genre-id').val(genreId);
            $('#update-genre-name').val(name);

            $('#updateGenreModal').modal('show');
        });
    }

    function loadContent(url, pushState = true) {
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'html',
            success: function(response) {
                let newContent = $('<div>').append($.parseHTML(response)).find('#main-content').html();
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
            error: function(xhr) {
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

    window.onpopstate = function(event) {
        if (event.state && event.state.path) {
            loadContent(event.state.path, false);
        }
    };


    initializeAll();
});
