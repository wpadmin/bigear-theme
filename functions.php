<?php
/**
 * bigear functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package bigear
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', wp_get_theme()->get('Version') );
}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/bigear-setup.php';





/**
 * Enqueue scripts and styles.
 */
require get_template_directory() . '/inc/enqueue-scripts.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Рубрики на базе Swiper.
 */
require get_template_directory() . '/inc/category-swiper.php';

/**
 *Bootstrap 5 Nav Walker.
 */
require get_template_directory() . '/inc/bootstrap_nav_walker.php';

/**
 * Ajax Search.
 */
require get_template_directory() . '/inc/ajax-search.php';