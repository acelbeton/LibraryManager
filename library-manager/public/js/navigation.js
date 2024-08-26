$(document).ready(function() {
    $('.add-translation').on('click', function() {
        let bookId = $(this).data('book_id');
        $('#translation-book-id').val(bookId); // Set the value of the hidden input field
    });

    function initializeAll() {
        reinitializeEventListeners();
    }

    function reinitializeEventListeners() {
        handleFormSubmission('#create-book-form', '#createBookModal', '#booksList');
        handleFormSubmission('#update-book-form', '#updateBookModal', '#booksList');
        handleFormSubmission('#translate-book-form', '#addTranslationModal', '#booksList');
        handleDeletion('.delete-book', '/books/destroy', '#booksList');
        handleSearchSubmission('#search-book', '#booksList');
        handleUpdate('.update-book');
        handleSuggestions('#suggestions', '#search_term');

        $('.select-language').on('change', function() {
            let bookId = $(this).data('book-id');
            let languageId = $(this).val();

            if (languageId === "default") {
                resetBookDataToDefault(bookId);
            } else {

                fetchTranslatedBookData(bookId, languageId);
            }
        });
    }

    function handleFormSubmission(formId, modalId, listId) {
        $(formId).off('submit');

        $(formId).on('submit', function(event) {
            event.preventDefault();

            const $form = $(this);
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
                                    reinitializeEventListeners();
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
                        reinitializeEventListeners();
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

    function handleDeletion(buttonClass, deleteUrl, listId) {
        $(document).off('click', buttonClass);

        $(document).on('click', buttonClass, function() {
            let $button = $(this);
            let itemId = $button.data('id');

            showBootstrapConfirmation('Are you sure you want to delete this book?', function() {
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
                            reinitializeEventListeners();
                            showToast('Book deleted successfully.', 'success');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'There was an error';

                        console.log('xhr.responseJSON:', xhr.responseJSON);

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


    function handleSuggestions(listId, inputId) {
        const form = $('#search-book');
        const suggestionsUrl = form.data('suggestions-url');
        $(document).on('keyup', inputId, function () {
            let query = $(this).val().trim();

            if (query.length > 1) {
                $.ajax({
                    url: suggestionsUrl,
                    method: 'GET',
                    data: { search_term: query },
                    success: function(response) {
                        $(listId).html(response.html);
                    },
                    error: function(xhr) {
                        console.error('ERROR SUGGESTION:', xhr);
                    }
                });
            } else {
                $(listId).empty();
            }
        });


        $(document).on('click', '.suggestion-item', function () {
            const title = $(this).data('title');
            $('#search_term').val(title);
            $('#suggestions').empty();
           $(listId).empty();
        });

        $('#search_term').on('focusout', function() {
            setTimeout(function() {
                $('#suggestions').empty();
            }, 100);
        });
    }

    function resetBookDataToDefault(bookId) {
        let bookRow = $('#book-' + bookId);
        let originalTitle = bookRow.data('original-title');
        let originalDescription = bookRow.data('original-description');
        bookRow.find('.book-title').text(originalTitle);
        bookRow.find('.book-description').text(originalDescription);
    }

    function fetchTranslatedBookData(bookId, languageId) {
        $.ajax({
           url: '/books/' + bookId + '/translate/' + languageId,
           method: 'GET',
           success: function(response) {
               let bookRow = $('#book-' + bookId);
               bookRow.find('.book-title').text(response.translated_title);
               bookRow.find('.book-description').text(response.translated_description);
           },
           error: function(xhr) {
               console.error('ERROR SUGGESTION:', xhr);
           }
        });
    }

    function handleUpdate(buttonClass) {
        $(document).off('click', buttonClass);

        $(document).on('click', buttonClass, function() {
            let $button = $(this);
            let bookId = $button.data('id');
            let title = $button.data('title');
            let description = $button.data('description');
            let authorId = $button.data('author_id');
            let genreId = $button.data('genre_id');
            let publisherId = $button.data('publisher_id');

            $('#update-book-id').val(bookId);
            $('#update-title').val(title);
            $('#update-description').val(description);
            $('#update-author_id').val(authorId);
            $('#update-genre_id').val(genreId);
            $('#update-publisher_id').val(publisherId);

            $('#updateBookModal').modal('show');
        });
    }

    function loadContent(url, pushState = true) {
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'html',
            success: function(response) {
                console.log('Response received:', response);
                let newContent = $('<div>').append($.parseHTML(response)).find('#main-content').html();
                if (newContent) {
                    $('#main-content').html(newContent);
                    if (pushState) {
                        window.history.pushState({ path: url }, '', url);
                    }
                    initializeAll();
                } else {
                    console.error('HTML content not found in the response');
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
