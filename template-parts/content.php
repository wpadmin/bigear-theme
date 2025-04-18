<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bigear
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('card w-100 mb-4'); ?>>
	<div class="row g-0">
		<header class="col-12 col-md-4 col-lg-3 col-xxl-2">
			<?php if (has_post_thumbnail()): ?>
				<?php bigear_post_thumbnail(array('class' => 'img-fluid w-100 h-auto')); ?>
			<?php else: ?>
				<div class="placeholder-thumbnail bg-light d-flex align-items-center justify-content-center" style="min-height: 200px;">
					<svg width="48" height="48" fill="currentColor" class="text-secondary opacity-50" viewBox="0 0 16 16">
						<path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
						<path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
					</svg>
				</div>
			<?php endif; ?>
		</header>

		<div class="col-12 col-md-8 col-lg-9 col-xxl-10">
			 <div class="card-body">
				<?php
					the_title( '<h2 class="h1 card-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark" class="text-decoration-none text-black">', '</a></h2>' );
				?>
				<?php
				the_excerpt();
				?>
				<?php bigear_entry_footer(); ?>
			</div>
		</div>
	</div> <!-- .row -->
</article><!-- #post-<?php the_ID(); ?> -->
