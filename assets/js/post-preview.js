jQuery(document).ready(function ($) {
    let timeout;
    const previewDelay = 300; // Задержка перед показом превью

    $('.navigation-item').hover(
        function () {
            const $this = $(this);
            const postId = $this.data('post-id');

            if (!postId) return;

            timeout = setTimeout(function () {
                if (!$this.data('image-loaded')) {
                    $.ajax({
                        url: postPreviewAjax.ajaxurl,
                        type: 'GET',
                        data: {
                            action: 'get_post_preview',
                            post_id: postId,
                            nonce: postPreviewAjax.nonce
                        },
                        success: function (response) {
                            if (response.success && response.data.image) {
                                const $previewContainer = $this.find('.preview-image-container');
                                const $image = $('<div class="preview-image"></div>');

                                $image.css({
                                    'background-image': `url(${response.data.image})`
                                });

                                $previewContainer.html($image);
                                $this.data('image-loaded', true);

                                setTimeout(() => {
                                    $image.addClass('show');
                                }, 50);
                            }
                        }
                    });
                } else {
                    $this.find('.preview-image').addClass('show');
                }
            }, previewDelay);
        },
        function () {
            clearTimeout(timeout);
            $(this).find('.preview-image').removeClass('show');
        }
    );
});