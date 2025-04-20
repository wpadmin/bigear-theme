<?php
/**
 * Bootstrap навигационное меню с поддержкой offcanvas для мобильных устройств
 *
 * Используется расположение меню 'primary'
 */

// Регистрируем новый класс для Walker_Nav_Menu для создания меню в стиле Bootstrap
class Bootstrap_Walker_Nav_Menu extends Walker_Nav_Menu {
    /**
     * Начало уровня вложенности меню
     */
    public function start_lvl(&$output, $depth = 0, $args = array()) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
    }

    /**
     * Начало элемента меню
     */
    public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $li_attributes = '';
        $class_names = $value = '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'nav-item';
        
        // Проверяем наличие дочерних элементов
        $has_children = in_array('menu-item-has-children', $classes);
        if ($has_children) {
            $classes[] = 'dropdown';
        }

        // Активный элемент
        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'active';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . $li_attributes . '>';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : '';

        // Классы для ссылок
        $atts['class'] = 'nav-link';
        
        // Если это dropdown, добавляем соответствующие атрибуты
        if ($has_children && $depth === 0) {
            $atts['class'] .= ' dropdown-toggle';
            $atts['data-bs-toggle'] = 'dropdown';
            $atts['role'] = 'button';
            $atts['aria-expanded'] = 'false';
        }

        // Активная ссылка
        if (in_array('current-menu-item', $classes)) {
            $atts['class'] .= ' active';
            $atts['aria-current'] = 'page';
        }

        // Элементы внутри dropdown
        if ($depth > 0) {
            $atts['class'] = 'dropdown-item';
        }

        // Disabled элементы
        if (in_array('disabled', $classes)) {
            $atts['class'] .= ' disabled';
            $atts['aria-disabled'] = 'true';
        }

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * Функция для отображения меню с поддержкой Bootstrap
 */
function _bootstrap_navbar_menu() {
    // Проверяем, зарегистрировано ли меню
    if (!has_nav_menu('primary')) {
        return;
    }
    ?>
    <!-- Десктопная версия меню -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-none d-lg-block">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                <?php echo get_bloginfo('name'); ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'depth'          => 2, // 1 = нет dropdown, 2 = есть dropdown
                    'container'      => false,
                    'menu_class'     => 'navbar-nav me-auto mb-2 mb-lg-0',
                    'fallback_cb'    => '__return_false',
                    'walker'         => new Bootstrap_Walker_Nav_Menu()
                ));
                ?>
                <form class="d-flex" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                    <input class="form-control me-2" type="search" placeholder="Поиск" aria-label="Search" name="s" value="<?php echo get_search_query(); ?>">
                    <button class="btn btn-outline-success" type="submit">Поиск</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Мобильная версия меню (offcanvas) -->
    <nav class="navbar bg-body-tertiary fixed-top d-lg-none">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                <?php echo get_bloginfo('name'); ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel"><?php echo get_bloginfo('name'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'depth'          => 2,
                        'container'      => false,
                        'menu_class'     => 'navbar-nav justify-content-end flex-grow-1 pe-3',
                        'fallback_cb'    => '__return_false',
                        'walker'         => new Bootstrap_Walker_Nav_Menu()
                    ));
                    ?>
                    <form class="d-flex mt-3" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <input class="form-control me-2" type="search" placeholder="Поиск" aria-label="Search" name="s" value="<?php echo get_search_query(); ?>">
                        <button class="btn btn-outline-success" type="submit">Поиск</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    <?php
}

/**
 * Обновленная версия функции bootstrap_navbar_menu с интеграцией AJAX-поиска
 */
function bootstrap_navbar_menu() {
    // Проверяем, зарегистрировано ли меню
    if (!has_nav_menu('primary')) {
        return;
    }
    ?>
    <!-- Десктопная версия меню -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary d-none d-lg-block">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                <?php echo get_bloginfo('name'); ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'depth'          => 2, // 1 = нет dropdown, 2 = есть dropdown
                    'container'      => false,
                    'menu_class'     => 'navbar-nav me-auto mb-2 mb-lg-0',
                    'fallback_cb'    => '__return_false',
                    'walker'         => new Bootstrap_Walker_Nav_Menu()
                ));
                ?>
                <?php ajax_search_form(); ?>
            </div>
        </div>
    </nav>

    <!-- Мобильная версия меню (offcanvas) -->
    <nav class="navbar bg-body-tertiary fixed-top d-lg-none">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo esc_url(home_url('/')); ?>">
                <?php echo get_bloginfo('name'); ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel"><?php echo get_bloginfo('name'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'depth'          => 2,
                        'container'      => false,
                        'menu_class'     => 'navbar-nav justify-content-end flex-grow-1 pe-3',
                        'fallback_cb'    => '__return_false',
                        'walker'         => new Bootstrap_Walker_Nav_Menu()
                    ));
                    ?>
                    <div class="mt-3">
                        <?php ajax_search_form(); ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <?php
}

/**
 * Проверка загрузки Bootstrap скриптов
 */
function check_bootstrap_scripts() {
    // Проверка, загружены ли скрипты Bootstrap
    $bootstrap_js_loaded = false;
    $bootstrap_css_loaded = false;
    
    // Проверяем зарегистрированные скрипты
    global $wp_scripts;
    if (!empty($wp_scripts)) {
        foreach ($wp_scripts->registered as $handle => $script) {
            if (strpos($handle, 'bootstrap') !== false || strpos($script->src, 'bootstrap') !== false) {
                $bootstrap_js_loaded = true;
                break;
            }
        }
    }
    
    // Проверяем зарегистрированные стили
    global $wp_styles;
    if (!empty($wp_styles)) {
        foreach ($wp_styles->registered as $handle => $style) {
            if (strpos($handle, 'bootstrap') !== false || strpos($style->src, 'bootstrap') !== false) {
                $bootstrap_css_loaded = true;
                break;
            }
        }
    }
    
    // Если Bootstrap не загружен, выдаем предупреждение в админке
    if (!$bootstrap_js_loaded || !$bootstrap_css_loaded) {
        add_action('admin_notices', 'bootstrap_missing_notice');
    }
}
add_action('wp_loaded', 'check_bootstrap_scripts');

/**
 * Вывод предупреждения, если Bootstrap не загружен
 */
function bootstrap_missing_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e('Внимание! Для корректной работы навигационного меню требуется Bootstrap 5. Пожалуйста, убедитесь, что Bootstrap CSS и JS правильно подключены в вашей теме.', 'text-domain'); ?></p>
    </div>
    <?php
}

/**
 * Пример загрузки Bootstrap, если он не загружен в теме
 * Раскомментируйте код ниже, если Bootstrap не подключен в вашей теме
 */
/*
function load_bootstrap_assets() {
    // Bootstrap CSS
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', array(), '5.3.0');
    
    // Bootstrap JS + Popper
    wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', array(), '5.3.0', true);
}
add_action('wp_enqueue_scripts', 'load_bootstrap_assets');
*/

/**
 * Как использовать:
 * 
 * 1. Добавьте этот код в functions.php вашей темы
 * 2. Если Bootstrap не загружен в вашей теме, раскомментируйте функцию load_bootstrap_assets
 * 3. Вызовите функцию bootstrap_navbar_menu() в нужном месте шаблона:
 *    <?php bootstrap_navbar_menu(); ?>
 */