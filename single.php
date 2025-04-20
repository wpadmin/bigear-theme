<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package bigear
 */

get_header();
?>

	<main id="primary" class="site-main">
		<div class="container-fluid container-lg">
				<?php
				while ( have_posts() ) :
					the_post();

					// Основной контент
					get_template_part( 'template-parts/content', 'single' );



					// Post Navigation
					$navigation = get_the_post_navigation(
						array(
							'prev_text' => '
								<div class="navigation-item prev p-4 rounded-3 bg-white position-relative overflow-hidden" data-post-id="%ID%">
									<span class="dashicons dashicons-arrow-left-alt2 navigation-arrow"></span>
									<div class="navigation-content">
										<div class="small text-primary fw-bold mb-2">' . esc_html__('Previous Post', 'bigear') . '</div>
										<div class="navigation-title h5 mb-0">%title</div>
										<div class="preview-image-container"></div>
									</div>
								</div>',
							'next_text' => '
								<div class="navigation-item next p-4 rounded-3 bg-white position-relative overflow-hidden text-end" data-post-id="%ID%">
									<span class="dashicons dashicons-arrow-right-alt2 navigation-arrow"></span>
									<div class="navigation-content">
										<div class="small text-primary fw-bold mb-2">' . esc_html__('Next Post', 'bigear') . '</div>
										<div class="navigation-title h5 mb-0">%title</div>
										<div class="preview-image-container"></div>
									</div>
								</div>',
						)
					);

					// Add post IDs to navigation
					$navigation = preg_replace_callback(
						'/data-post-id="%ID%"/',
						function($matches) {
							global $post;
							$adjacent_post = get_adjacent_post(false, '', true);
							return 'data-post-id="' . ($adjacent_post ? $adjacent_post->ID : '') . '"';
						},
						$navigation
					);

					$navigation = str_replace(
						array('nav-links', 'nav-previous', 'nav-next'),
						array('row navigation-wrapper g-4', 'col-md-6 nav-previous', 'col-md-6 nav-next'),
						$navigation
					);

					echo $navigation;

					// If comments are open or we have at least one comment, load up the comment template.
					if ( comments_open() || get_comments_number() ) :
						comments_template();
					endif;

				endwhile; // End of the loop.
				?>
		</div>
	</main><!-- #main -->

<?php
//get_sidebar();
get_footer();
