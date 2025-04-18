<?php
/**
 * The template for displaying Front page
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bigear
 */

get_header();
?>

	<main id="primary" class="site-main">
        <div class="container-fluid container-lg">
            <?php
            // современные любовные романы
            echo do_shortcode('[category_swiper id="78" show_title="false"]');
            // русская классика
            echo do_shortcode('[category_swiper id="271"]');
            // современная русская литература
            echo do_shortcode('[category_swiper id="192"]');
            // сказки
            echo do_shortcode('[category_swiper id="94"]');
            // современные детективы
            echo do_shortcode('[category_swiper id="235"]');
            // саморазвитие / личностный рост
            echo do_shortcode('[category_swiper id="230"]');
            ?>
        </div>
	</main><!-- #main -->

<?php
get_footer();
