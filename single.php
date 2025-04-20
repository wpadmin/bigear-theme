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

					// Похожие книги

					// Получаем ID текущего поста
					$current_post_id = get_the_ID();

					// Получаем категории текущего поста
					$categories = get_the_category($current_post_id);

					if (!empty($categories)) {
						$category_ids = array();
						foreach ($categories as $category) {
							$category_ids[] = $category->term_id;
						}
						
						// Параметры для запроса похожих постов
						$args = array(
							'category__in' => $category_ids,
							'post__not_in' => array($current_post_id), // Исключаем текущий пост
							'posts_per_page' => 5, // Количество похожих книг
							'orderby' => 'rand' // Случайный порядок
						);
						
						$related_query = new WP_Query($args);
						
						if ($related_query->have_posts()) :
					?>
							<section class="related-books py-5 mt-4">
								<div class="container">
									<h2 class="text-center mb-4 h1">Похожие книги</h2>
									
									<div class="bookshelf-container position-relative mb-5">
										<div class="row justify-content-center gx-4 gy-5 mb-4">
											<?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
												<div class="col-6 col-sm-4 col-md-3 col-lg-2">
													<div class="book-wrapper position-relative">
														<a href="<?php the_permalink(); ?>" class="book d-block text-decoration-none" title="<?php the_title_attribute(); ?>">
															<div class="book-inner">
																<div class="book-cover shadow rounded">
																	<?php if (has_post_thumbnail()) : ?>
																		<?php the_post_thumbnail('medium', array('class' => 'img-fluid rounded')); ?>
																	<?php else : ?>
																		<img src="<?php echo get_template_directory_uri(); ?>/images/default-book.jpg" alt="<?php the_title_attribute(); ?>" class="img-fluid rounded">
																	<?php endif; ?>
																</div>
																<div class="book-spine"></div>
																<div class="book-side"></div>
															</div>
															<p class="book-title small text-center mt-2 text-truncate" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php the_title_attribute(); ?>">
																<?php the_title(); ?>
															</p>
														</a>
													</div>
												</div>
											<?php endwhile; ?>
										</div>
										<div class="bookshelf position-relative mx-auto"></div>
									</div>
								</div>
							</section>
					<?php
						endif;
						
						// Сбрасываем данные запроса
						wp_reset_postdata();
					}

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
