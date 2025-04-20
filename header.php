<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package bigear
 */

// Получаем количество опубликованных постов
$all_posts = bigear_count_published_posts();
?>
<!doctype html>
<!--
∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿
     ★ Crafted by github.com/wpadmin
∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿∿
-->
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?> 
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'bigear' ); ?></a>
    
	<!-- Оверлей с спиннером загрузки используя классы Bootstrap -->
    <div class="overlay d-flex justify-content-center align-items-center" id="loadingOverlay">
        <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
    </div>
	
	<header id="masthead" class="site-header">

		<div class="d-flex justify-content-center align-items-center">
			<div class="d-flex align-items-center">
				<?php
				// Check if the site has a custom logo
				if (has_custom_logo()) {
					// Get the custom logo ID
					$custom_logo_id = get_theme_mod('custom_logo');
					
					// Get the logo image attributes (alt text, src, width, height)
					$logo_attributes = wp_get_attachment_image_src($custom_logo_id, 'full');
					
					// Define custom logo attributes
					$logo_width = '150px'; // Set desired width for the logo
					$logo_class = 'site-logo img-fluid me-3'; // Add classes for styling
					
					// Check if we have valid logo attributes
					if ($logo_attributes) {
						// Create custom logo HTML with specified width and classes
						$custom_logo_html = sprintf(
							'<a href="%1$s" class="custom-logo-link" rel="home"><img src="%2$s" class="%3$s" width="%4$s" alt="%5$s"></a>',
							esc_url(home_url('/')),
							esc_url($logo_attributes[0]),
							esc_attr($logo_class),
							esc_attr($logo_width),
							esc_attr(get_bloginfo('name'))
						);
						
						// Output the custom logo
						echo $custom_logo_html;
					}
				} else {
					// Optional: Display a fallback if no logo is set
					echo '<span class="site-logo-placeholder me-3"><i class="fa fa-book fs-1"></i></span>';
				}
				?>
				
				<div class="ms-3">
					<?php if ( is_front_page() ) : ?>
						<p class="display-2 mb-0 text-great-vibes">
							<?php bloginfo( 'name' ); ?>
						</p>
					<?php else : ?>
						<p class="display-2 mb-0 text-great-vibes">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" class="text-decoration-none text-black">
								<?php bloginfo( 'name' ); ?>
							</a>
						</p>
					<?php endif;
					
					$bigear_description = get_bloginfo( 'description', 'display' );
					if ( $bigear_description || is_customize_preview() ) : ?>
						<p class="lead mb-0">
							<?php echo $bigear_description . " (опубликовано {$all_posts} книг)"; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Bootstrap Меню -->
 		<?php bootstrap_navbar_menu(); ?>

		<div class="container mt-3">
			<!--Хлебные крошки -->
			<?php if (!is_front_page()) : ?>
				<?php bigear_breadcrumbs(); ?>
			<?php endif; ?>
		</div>
	</header><!-- #masthead -->
