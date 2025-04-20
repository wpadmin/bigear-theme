<?php
/**
 * AJAX-поиск для WordPress с автозаполнением и карточками результатов
 */

/**
 * Функция для регистрации скриптов AJAX-поиска
 */
function register_ajax_search_scripts() {
    // Регистрируем и подключаем JS-скрипт для AJAX-поиска
    wp_enqueue_script(
        'ajax-search-script',
        get_template_directory_uri() . '/assets/js/ajax-search.js',
        array('jquery'),
        '1.0',
        true
    );
    
    // Передаем данные в JavaScript
    wp_localize_script(
        'ajax-search-script',
        'ajax_search_params',
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('ajax_search_nonce'),
            'min_chars' => 3, // Минимальное количество символов для начала поиска
        )
    );
}
add_action('wp_enqueue_scripts', 'register_ajax_search_scripts');

/**
 * Обработчик AJAX-запроса для поиска
 */
function ajax_search_handler() {
    // Проверяем nonce для безопасности
    check_ajax_referer('ajax_search_nonce', 'security');
    
    // Получаем поисковый запрос
    $search_query = isset($_POST['search_query']) ? sanitize_text_field($_POST['search_query']) : '';
    
    // Проверяем длину запроса
    if (strlen($search_query) < 2) {
        wp_send_json_error(array('message' => 'Запрос слишком короткий'));
        wp_die();
    }
    
    // Выполняем поиск
    $args = array(
        's' => $search_query,
        'post_type' => 'post', // Можно ограничить типами записей или 'any'
        'post_status' => 'publish',
        'posts_per_page' => 8, // Ограничиваем количество результатов
    );
    
    $search_query = new WP_Query($args);
    $results = array();
    
    if ($search_query->have_posts()) {
        while ($search_query->have_posts()) {
            $search_query->the_post();
            
            // Получаем миниатюру
            $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
            if (!$thumbnail) {
                $thumbnail = get_template_directory_uri() . '/images/no-image.jpg'; // Заглушка если нет миниатюры
            }
            
            // Формируем результат
            $results[] = array(
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'excerpt' => wp_trim_words(get_the_excerpt(), 10), // Обрезаем текст до 10 слов
                'url' => get_permalink(),
                'thumbnail' => $thumbnail,
                'post_type' => get_post_type(),
                'date' => get_the_date('j F Y'),
            );
        }
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'results' => $results,
            'count' => count($results),
            'message' => sprintf(_n('%s результат найден', '%s результатов найдено', count($results), 'text-domain'), count($results))
        ));
    } else {
        wp_send_json_error(array('message' => 'Ничего не найдено'));
    }
    
    wp_die();
}
add_action('wp_ajax_ajax_search', 'ajax_search_handler'); // Для авторизованных пользователей
add_action('wp_ajax_nopriv_ajax_search', 'ajax_search_handler'); // Для неавторизованных пользователей

/**
 * Функция формы поиска для меню
 */
function ajax_search_form() {
    ?>
    <div class="search-container position-relative">
        <form class="ajax-search-form d-flex" role="search">
            <input class="form-control me-2" type="search" placeholder="Поиск" aria-label="Search" name="s" id="ajax-search-input" autocomplete="off">
            <button class="btn btn-outline-success" type="submit">
                <span class="search-text">Поиск</span>
                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
        </form>
        <!-- Контейнер для результатов поиска -->
        <div class="ajax-search-results position-absolute w-100 bg-white shadow-sm rounded d-none" style="z-index: 1050; max-height: 400px; overflow-y: auto;">
            <!-- Сюда будут добавлены результаты поиска через JavaScript -->
        </div>
    </div>
    <?php
}