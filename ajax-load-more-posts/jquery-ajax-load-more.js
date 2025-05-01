jQuery(document).ready(function ($) {
    let currentPage = 1;

    function renderPost(post) {
        return `<div class="jquery-post">
            <h2>${post.title}</h2>
            <p>${post.excerpt}</p>
        </div>`;
    }

    function loadPosts() {
        $.ajax({
            type: 'POST',
            url: ajaxurl.ajaxurl,
            data: {
                action: 'jquery_load_more_posts',
                nonce: ajaxurl.nonce,
                page: currentPage
            },
            success: function (response) {
                if (response.success && response.data.length) {
                    response.data.forEach(post => {
                        $('#jquery-posts-container').append(renderPost(post));
                    });
                } else {
                    $('#jquery-load-more').prop('disabled', true).text('No More Posts');
                }
            },
            error: function () {
                $('#jquery-load-more').prop('disabled', true).text('Error Loading Posts');
            }
        });
    }

    // Initial load
    loadPosts();
    currentPage++;

    $('#jquery-load-more').on('click', function () {
        loadPosts();
        currentPage++;
    });
});
