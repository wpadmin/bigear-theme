<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package bigear
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="сontainer-fluid container-lg py-5 text-center">
        <?php
        // Получаем название сайта
        $site_name = get_bloginfo('name');
        
        // Получаем текущий год
        $current_year = date('Y');
        
        // Выводим копирайт
        printf(
            '<span class="copyright">&copy; %d %s. Все права защищены.</span>',
            esc_html($current_year),
            esc_html($site_name)
        );
        ?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
