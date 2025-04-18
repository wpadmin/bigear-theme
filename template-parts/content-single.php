<?php
/**
 * Template part for displaying page content in single.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bigear
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class('row'); ?>>
	<header class="col-12 col-lg-8 offset-lg-2">
		<?php the_title( '<h1 class="display-3 my-5">', '</h1>' ); ?>
	</header><!-- header h1 -->

	<div class="entry-content col-12 col-lg-8 offset-lg-2 lead">

        <div class="row mb-3">
            <div class="col-12 col-md-5">
                <?php bigear_post_thumbnail(); ?>
            </div>
            <div class="col-12 col-md-7">
            <?php
            // Получаем ID поста один раз
            $post_id = get_the_ID();

            // Массив метаданных для проверки и вывода
            $meta_fields = [
                'performed_by' => [ 
                    'label' => 'Чтец',
                    'type' => 'text'
                ],
                'publisher' => [
                    'label' => 'Правообладатель',
                    'type' => 'text'
                ],
                'year' => [
                    'label' => 'Год издания',
                    'type' => 'text'
                ],
                'format' => [
                    'label' => 'Формат',
                    'type' => 'text'
                ],
                'partner-link' => [
                    'label' => 'Купить на Литрес',
                    'type' => 'button',
                    'class' => 'text-white btn btn-lg bg-primary bg-gradient rounded-pill px-4 py-3 mt-3 mx-1'
                ] 
            ]; 

            // Вывод тегов (автор)
            $post_tags = get_the_tags();
            if ($post_tags) {
                printf(
                    '<div class="tags-links"><strong>Автор: </strong><a href="%s" class="text-dark decoration-none" target="_blank">%s</a></div>',
                    esc_url(get_tag_link($post_tags[0]->term_id)),
                    esc_html($post_tags[0]->name)
                );
            }

            // Получаем все мета-поля одним запросом
            $all_meta = get_post_meta($post_id);

            // Вывод метаданных в цикле
            foreach ($meta_fields as $key => $field) {
                if (isset($all_meta[$key][0])) {
                    $value = $all_meta[$key][0];
                    
                    if ($field['type'] === 'text') {
                        printf(
                            '<p class="mb-1"><strong>%s:</strong> %s</p>',
                            esc_html($field['label']),
                            esc_html($value)
                        );
                    } elseif ($field['type'] === 'button' && !empty($value)) {
                        printf(
                            '<a href="%s" class="%s" target="_blank">%s</a>',
                            esc_url($value),
                            esc_attr($field['class']),
                            esc_html($field['label'])
                        );
                    }
                }
            }

            // Ссылка на фрагмент
            if (metadata_exists('post', get_the_ID(), 'fragment')) {
                $fragment = esc_url(get_post_meta(get_the_ID(), 'fragment', true));
               ?>
              <div class="audio-player">
                    <h3>Слушать аудиофрагмент</h3>
                    <audio id="audio-element" src="<?php echo $fragment;?>" preload="metadata"></audio>
                    
                    <div class="audio-controls">
                    <button class="play-pause-btn" id="play-pause-btn">▶</button>
                    
                    <div class="progress-container" id="progress-container">
                        <div class="progress-bar" id="progress-bar"></div>
                    </div>
                    
                    <div class="time-display">
                        <span id="current-time">0:00</span> / <span id="duration">0:00</span>
                    </div>
                    
                    <div class="volume-container">
                        <input type="range" class="volume-slider" id="volume-slider" min="0" max="1" step="0.01" value="1">
                    </div>
                    </div>
                </div>
               <?php
            }
            ?>

            </div>
        </div>

		<?php the_content(); ?>

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
