<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package bigear
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function bigear_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'bigear_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function bigear_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'bigear_pingback_header' );


/**
 * Выводит хлебные крошки Bootstrap 5.3 для WordPress
 * 
 * @param bool $echo Выводить хлебные крошки или возвращать в виде строки
 * @return string|void
 */
function bigear_breadcrumbs($echo = true) {
    // Инициализируем переменную для сбора HTML-кода
    $html = '<nav aria-label="breadcrumb">';
    $html .= '<ol class="breadcrumb">';
    
    // Всегда добавляем ссылку на главную страницу
    $html .= '<li class="breadcrumb-item"><a href="' . esc_url(home_url()) . '">Главная</a></li>';
    
    // На главной странице показываем только пункт "Главная"
    if (is_front_page()) {
        // Убираем дубликат "Главная" и просто делаем активным первый пункт
        $html = '<nav aria-label="breadcrumb">';
        $html .= '<ol class="breadcrumb">';
        $html .= '<li class="breadcrumb-item active" aria-current="page">Главная</li>';
    } 
    // Для страниц поиска
    elseif (is_search()) {
        $html .= '<li class="breadcrumb-item active" aria-current="page">Результаты поиска: "' . esc_html(get_search_query()) . '"</li>';
    }
    // Для страниц категорий
    elseif (is_category()) {
        $category = get_queried_object();
        
        // Проверяем, есть ли родительские категории
        if ($category->parent != 0) {
            // Получаем все родительские категории
            $parents = get_ancestors($category->term_id, 'category');
            
            // Перебираем родительские категории в обратном порядке (от корня к текущей)
            foreach (array_reverse($parents) as $parent_id) {
                $parent = get_term($parent_id, 'category');
                $html .= '<li class="breadcrumb-item"><a href="' . esc_url(get_category_link($parent_id)) . '">' . esc_html($parent->name) . '</a></li>';
            }
        }
        
        $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html(single_cat_title('', false)) . '</li>';
    }
    // Для страниц тегов
    elseif (is_tag()) {
        $html .= '<li class="breadcrumb-item active" aria-current="page">Записи с тегом: "' . esc_html(single_tag_title('', false)) . '"</li>';
    }
    // Для архивов по дате
    elseif (is_day() || is_month() || is_year()) {
        $html .= '<li class="breadcrumb-item"><a href="' . esc_url(get_year_link(get_the_time('Y'))) . '">' . esc_html(get_the_time('Y')) . '</a></li>';
        
        if (is_month() || is_day()) {
            $html .= '<li class="breadcrumb-item"><a href="' . esc_url(get_month_link(get_the_time('Y'), get_the_time('m'))) . '">' . esc_html(get_the_time('F')) . '</a></li>';
        }
        
        if (is_day()) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_time('d')) . '</li>';
        } elseif (is_month()) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_time('F')) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_time('Y')) . '</li>';
        }
    }
    // Для страниц авторов
    elseif (is_author()) {
        $html .= '<li class="breadcrumb-item active" aria-current="page">Архив автора: ' . esc_html(get_the_author()) . '</li>';
    }
    // Для отдельных записей
    elseif (is_single()) {
        if (get_post_type() == 'post') {
            $categories = get_the_category();
            if ($categories) {
                $category = $categories[0];
                
                // Получаем иерархию категорий
                $parents = get_ancestors($category->term_id, 'category');
                
                // Выводим родительские категории в обратном порядке
                foreach (array_reverse($parents) as $parent_id) {
                    $parent = get_term($parent_id, 'category');
                    $html .= '<li class="breadcrumb-item"><a href="' . esc_url(get_category_link($parent_id)) . '">' . esc_html($parent->name) . '</a></li>';
                }
                
                // Выводим текущую категорию
                $html .= '<li class="breadcrumb-item"><a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a></li>';
            }
        } elseif (get_post_type() != 'page') {
            // Для произвольных типов записей
            $post_type = get_post_type_object(get_post_type());
            if ($post_type) {
                $html .= '<li class="breadcrumb-item"><a href="' . esc_url(get_post_type_archive_link(get_post_type())) . '">' . esc_html($post_type->labels->name) . '</a></li>';
            }
        }
        
        $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
    }
    // Для статических страниц
    elseif (is_page()) {
        $post = get_post(get_the_ID());
        
        // Если у страницы есть родительские страницы
        if ($post->post_parent) {
            // Собираем все родительские страницы
            $parents = array();
            $parent_id = $post->post_parent;
            
            while ($parent_id) {
                $parent_page = get_post($parent_id);
                $parents[] = array(
                    'id' => $parent_id,
                    'title' => get_the_title($parent_id)
                );
                $parent_id = $parent_page->post_parent;
            }
            
            // Выводим родительские страницы в обратном порядке
            foreach (array_reverse($parents) as $parent) {
                $html .= '<li class="breadcrumb-item"><a href="' . esc_url(get_permalink($parent['id'])) . '">' . esc_html($parent['title']) . '</a></li>';
            }
        }
        
        $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html(get_the_title()) . '</li>';
    }
    // Для архивов
    elseif (is_archive()) {
        $post_type = get_post_type_object(get_post_type());
        if ($post_type) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc_html($post_type->labels->name) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item active" aria-current="page">Архивы</li>';
        }
    }
    // Для страницы 404
    elseif (is_404()) {
        $html .= '<li class="breadcrumb-item active" aria-current="page">Страница не найдена</li>';
    }
    
    $html .= '</ol>';
    $html .= '</nav>';
    
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}


/**
 * Отображение пагинации на базе классов Bootstrap 5.3
 */
function bigear_pagination() {
    global $wp_query;
    
    // Не выводим пагинацию, если страниц меньше двух
    if ($wp_query->max_num_pages <= 1) {
        return;
    }
    
    $paged = get_query_var('paged') ? get_query_var('paged') : 1;
    $max_pages = $wp_query->max_num_pages;
    
    // Начало пагинации с использованием классов Bootstrap 5.3
    echo '<nav aria-label="Навигация по страницам">';
    echo '<ul class="pagination pagination-lg justify-content-center">';
    
    // Кнопка "Предыдущая"
    if ($paged > 1) {
        echo '<li class="page-item">';
        echo '<a class="page-link" href="' . get_pagenum_link($paged - 1) . '" aria-label="Предыдущая">';
        echo '<span aria-hidden="true">&laquo;</span> <span class="d-none d-sm-inline">Назад</span>';
        echo '</a>';
        echo '</li>';
    } else {
        echo '<li class="page-item disabled">';
        echo '<span class="page-link"><span aria-hidden="true">&laquo;</span> <span class="d-none d-sm-inline">Назад</span></span>';
        echo '</li>';
    }
    
    // Максимум отображаемых страниц
    $range = 2;
    
    // Всегда показываем первую страницу
    if ($paged > 1) {
        echo '<li class="page-item"><a class="page-link" href="' . get_pagenum_link(1) . '">1</a></li>';
    } else {
        echo '<li class="page-item active" aria-current="page"><span class="page-link">1</span></li>';
    }
    
    // Многоточие после первой страницы, если текущая страница далеко
    if ($paged > $range + 1) {
        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }
    
    // Страницы рядом с текущей
    for ($i = max(2, $paged - $range); $i <= min($max_pages - 1, $paged + $range); $i++) {
        if ($paged == $i) {
            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $i . '</span></li>';
        } else {
            echo '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($i) . '">' . $i . '</a></li>';
        }
    }
    
    // Многоточие перед последней страницей, если необходимо
    if ($paged < $max_pages - $range - 1) {
        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }
    
    // Всегда показываем последнюю страницу, если она не первая
    if ($max_pages > 1) {
        if ($paged == $max_pages) {
            echo '<li class="page-item active" aria-current="page"><span class="page-link">' . $max_pages . '</span></li>';
        } else {
            echo '<li class="page-item"><a class="page-link" href="' . get_pagenum_link($max_pages) . '">' . $max_pages . '</a></li>';
        }
    }
    
    // Кнопка "Следующая"
    if ($paged < $max_pages) {
        echo '<li class="page-item">';
        echo '<a class="page-link" href="' . get_pagenum_link($paged + 1) . '" aria-label="Следующая">';
        echo '<span class="d-none d-sm-inline">Вперёд</span> <span aria-hidden="true">&raquo;</span>';
        echo '</a>';
        echo '</li>';
    } else {
        echo '<li class="page-item disabled">';
        echo '<span class="page-link"><span class="d-none d-sm-inline">Вперёд</span> <span aria-hidden="true">&raquo;</span></span>';
        echo '</li>';
    }
    
    echo '</ul>';
    echo '</nav>';
}


/**
 * Подсчет опубликованных записей в WordPress с форматированием тысячных
 *
 * @param array $args Аргументы для фильтрации подсчета
 * @param string $thousands_sep Разделитель тысяч (по умолчанию пробел)
 * @param string $decimal_point Разделитель дробной части (по умолчанию запятая)
 * @param int $decimals Количество знаков после запятой (по умолчанию 0)
 * @return string Отформатированное количество опубликованных записей
 */

/**
 * Примеры использования функции:
 *
 * // Подсчет всех опубликованных записей с форматированием (например: 1 234)
 * $all_posts = bigear_count_published_posts();
 *
 * // Подсчет с использованием запятой как разделителя тысяч (например: 1,234)
 * $all_posts_comma = bigear_count_published_posts(array(), ',', '.', 0);
 *
 * // Подсчет записей в категории без форматирования (возвращает integer)
 * $category_posts = bigear_count_published_posts(array(
 *     'category' => 'novels',
 *     'format'   => false
 * ));
 *
 * // Создание шорткода для вывода количества записей
 * function bigear_post_count_shortcode($atts) {
 *     $atts = shortcode_atts(array(
 *         'post_type' => 'post',
 *         'category'  => '',
 *         'taxonomy'  => '',
 *         'term'      => '',
 *     ), $atts, 'post_count');
 *     
 *     return bigear_count_published_posts($atts);
 * }
 * add_shortcode('post_count', 'bigear_post_count_shortcode');
 */
function bigear_count_published_posts($args = array(), $thousands_sep = ' ', $decimal_point = ',', $decimals = 0) {
    // Установка значений по умолчанию
    $defaults = array(
        'post_type'    => 'post',      // Тип записи (post, page, custom_post_type)
        'category'     => '',          // ID категории или slug
        'author'       => '',          // ID автора
        'taxonomy'     => '',          // Название пользовательской таксономии
        'term'         => '',          // Термин таксономии (ID или slug)
        'date_query'   => array(),     // Параметры запроса по дате
        'meta_key'     => '',          // Ключ произвольного поля
        'meta_value'   => '',          // Значение произвольного поля
        'format'       => true,        // Форматировать результат с разделителями
    );

    // Объединение переданных аргументов с значениями по умолчанию
    $args = wp_parse_args($args, $defaults);

    // Базовые параметры запроса
    $query_args = array(
        'post_type'      => $args['post_type'],
        'post_status'    => 'publish',
        'posts_per_page' => -1,        // Получить все записи
        'fields'         => 'ids',     // Получить только ID для оптимизации запроса
        'no_found_rows'  => true,      // Оптимизация запроса
    );

    // Добавление фильтра по категории, если указан
    if (!empty($args['category'])) {
        if (is_numeric($args['category'])) {
            $query_args['cat'] = intval($args['category']);
        } else {
            $query_args['category_name'] = $args['category'];
        }
    }

    // Добавление фильтра по автору, если указан
    if (!empty($args['author'])) {
        $query_args['author'] = intval($args['author']);
    }

    // Добавление фильтра по произвольной таксономии, если указаны
    if (!empty($args['taxonomy']) && !empty($args['term'])) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => $args['taxonomy'],
                'field'    => is_numeric($args['term']) ? 'term_id' : 'slug',
                'terms'    => $args['term'],
            ),
        );
    }

    // Добавление фильтра по дате, если указан
    if (!empty($args['date_query'])) {
        $query_args['date_query'] = $args['date_query'];
    }

    // Добавление фильтра по произвольным полям, если указаны
    if (!empty($args['meta_key'])) {
        $query_args['meta_key'] = $args['meta_key'];
        
        if (!empty($args['meta_value'])) {
            $query_args['meta_value'] = $args['meta_value'];
        }
    }

    // Выполнение запроса
    $query = new WP_Query($query_args);
    $count = $query->post_count;
    
    // Форматирование результата с разделителями тысячных, если требуется
    if ($args['format']) {
        return number_format($count, $decimals, $decimal_point, $thousands_sep);
    }
    
    // Возвращение неформатированного количества
    return $count;
}