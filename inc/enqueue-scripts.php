<?php

/**
 * Enqueue scripts and styles.
 */
function bigear_scripts()
{
    wp_enqueue_style('bigear-style', get_stylesheet_uri(), array(), _S_VERSION);

    // CSS
    wp_enqueue_style(
        'theme-styles',
        get_template_directory_uri() . '/public/css/main.css',
        array(),
        _S_VERSION
    );

    // JavaScript
    wp_enqueue_script(
        'theme-scripts',
        get_template_directory_uri() . '/public/js/main.js',
        array(),
        _S_VERSION,
        true
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // Подключаем иконки Dashicons
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'bigear_scripts');

/**
 * Функция для оптимизированного подключения шрифта Great Vibes с preload
 *
 */
function bigear_enqueue_optimized_great_vibes_font()
{
    // Добавляем preload для шрифта
    add_action('wp_head', function () {
        echo '<link rel="preload" href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap&subset=latin,cyrillic" as="style" crossorigin="anonymous">';
        echo '<link rel="preload" href="https://fonts.gstatic.com/s/greatvibes/v15/RWmMoKWR9v4ksMfaWd_JN9XKiaQ6DQ.woff2" as="font" type="font/woff2" crossorigin="anonymous">';
    }, 1);

    // Подключаем стиль шрифта
    wp_enqueue_style(
        'google-font-great-vibes',
        'https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap&subset=latin,cyrillic',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'bigear_enqueue_optimized_great_vibes_font');


/**
 * Функция 
 * Регистрируем AJAX endpoint для отображения превью поста
 */
function bigear_register_post_preview_ajax() {
    wp_enqueue_script('post-preview', get_template_directory_uri() . '/assets/js/post-preview.js', array('jquery'), '1.0', true);
    wp_localize_script('post-preview', 'postPreviewAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('post_preview_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'bigear_register_post_preview_ajax');

// AJAX handler
function bigear_get_post_preview() {
    check_ajax_referer('post_preview_nonce', 'nonce');
    $post_id = intval($_GET['post_id']);
    
    if ($post_id) {
        $image = get_the_post_thumbnail_url($post_id, 'medium');
        wp_send_json_success(array(
            'image' => $image ?: '',
        ));
    }
    wp_send_json_error();
}
add_action('wp_ajax_get_post_preview', 'bigear_get_post_preview');
add_action('wp_ajax_nopriv_get_post_preview', 'bigear_get_post_preview');