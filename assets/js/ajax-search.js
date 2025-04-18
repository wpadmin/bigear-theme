jQuery(document).ready(function ($) {
    const searchForm = $('#ajax-search-form');
    const searchInput = $('#search-input');
    const searchSuggestions = $('#search-suggestions');
    const searchResults = $('#search-results');
    let searchTimeout;

    // Function to perform the search
    function performSearch(showSuggestions = false) {
        const searchData = {
            action: 'bigear_ajax_search',
            security: bigearAjax.nonce,
            search: searchInput.val(),
            author: $('#author-select').val(),
            year: $('#year-select').val(),
            suggestions: showSuggestions
        };

        $.ajax({
            url: bigearAjax.ajaxurl,
            type: 'POST',
            data: searchData,
            beforeSend: function () {
                if (!showSuggestions) {
                    searchResults.html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                }
            },
            success: function (response) {
                if (showSuggestions) {
                    if (response.suggestions && response.suggestions.length > 0) {
                        let suggestionsHtml = '';
                        response.suggestions.forEach(suggestion => {
                            suggestionsHtml += `<a class="dropdown-item" href="#">${suggestion}</a>`;
                        });
                        searchSuggestions.html(suggestionsHtml).show();
                    } else {
                        searchSuggestions.hide();
                    }
                } else {
                    searchResults.html(response.html);
                }
            },
            error: function () {
                if (!showSuggestions) {
                    searchResults.html('<div class="alert alert-danger">Error occurred while searching. Please try again.</div>');
                }
            }
        });
    }

    // Handle search input with debounce for suggestions
    searchInput.on('input', function () {
        clearTimeout(searchTimeout);
        const query = $(this).val();

        if (query.length >= 3) {
            searchTimeout = setTimeout(() => {
                performSearch(true);
            }, 300);
        } else {
            searchSuggestions.hide();
        }
    });

    // Handle suggestion clicks
    searchSuggestions.on('click', '.dropdown-item', function (e) {
        e.preventDefault();
        searchInput.val($(this).text());
        searchSuggestions.hide();
        performSearch();
    });

    // Handle form submission
    searchForm.on('submit', function (e) {
        e.preventDefault();
        searchSuggestions.hide();
        performSearch();
    });

    // Handle select changes
    $('#author-select, #year-select').on('change', function () {
        performSearch();
    });

    // Close suggestions when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-filters-container').length) {
            searchSuggestions.hide();
        }
    });
});