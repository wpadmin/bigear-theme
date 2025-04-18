<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bigear
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php
		the_content();

        wp_link_pages(
            array(
                'before'           => '<nav aria-label="Page navigation"><div class="pagination justify-content-center my-4">',
                'after'            => '</div></nav>',
                'link_before'      => '<span class="page-item"><a class="page-link" href="#">',
                'link_after'       => '</a></span>',
                'separator'        => '', // Remove default separator since we're using Bootstrap styling
                'nextpagelink'     => '<span aria-hidden="true">&raquo;</span>',
                'previouspagelink' => '<span aria-hidden="true">&laquo;</span>',
                'pagelink'         => '%',
                'echo'            => 1
            )
        );
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="screen-reader-text">%s</span>', 'bigear' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
