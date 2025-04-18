<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package bigear
 */

if ( ! function_exists( 'bigear_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function bigear_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'bigear' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'bigear_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function bigear_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'bigear' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'bigear_entry_footer' ) ) :
/**
 * Выводит мета-информацию о записи с использованием Bootstrap 5.3
 */
function bigear_entry_footer() {
    // Проверяем тип записи
    if ('post' === get_post_type()) {
        echo '<div class="card-meta mt-3">';
        
        // Жанр (категории)
        $categories_list = get_the_category_list(esc_html__(', ', 'bigear'));
        if ($categories_list) {
            echo '<div class="meta-item mb-2">';
            echo '<span class="meta-label badge bg-primary me-2">Жанр</span>';
            echo '<span class="meta-value">' . $categories_list . '</span>';
            echo '</div>';
        }
        
        // Автор (теги)
        $tags_list = get_the_tag_list('', esc_html_x(', ', 'list item separator', 'bigear'));
        if ($tags_list) {
            echo '<div class="meta-item mb-2">';
            echo '<span class="meta-label badge bg-secondary me-2">Автор</span>';
            echo '<span class="meta-value">' . $tags_list . '</span>';
            echo '</div>';
        }
        
        // Серия книг
        $book_series = get_the_term_list(get_the_ID(), 'book_series', '', esc_html_x(', ', 'list item separator', 'bigear'));
        if ($book_series) {
            echo '<div class="meta-item mb-2">';
            echo '<span class="meta-label badge bg-info text-dark me-2">Серия</span>';
            echo '<span class="meta-value">' . $book_series . '</span>';
            echo '</div>';
        }
        
        echo '</div>'; // .card-meta
    }

    // Ссылка редактирования (только для администраторов)
    if (current_user_can('edit_post', get_the_ID())) {
        echo '<div class="edit-link mt-3">';
        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Редактировать <span class="screen-reader-text">%s</span>', 'bigear'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                wp_kses_post(get_the_title())
            ),
            '<small><a class="btn btn-sm btn-outline-secondary" href="',
            '"><i class="bi bi-pencil-square me-1"></i>',
            '</a></small>'
        );
        echo '</div>';
    }
}
endif;

if ( ! function_exists( 'bigear_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function bigear_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'class' => 'img-fluid',
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'class' => 'img-fluid',
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;
