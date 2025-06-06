<?php

/**
 * Добавляем скрипты и стили для Swiper
 */
function category_swiper_enqueue_scripts()
{
	// Подключаем Swiper CSS
	wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css', array(), '10.0.0');

	// Подключаем Swiper JS
	wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', array(), '10.0.0', true);

	// Подключаем наш скрипт
	wp_enqueue_script('category-swiper-js', get_template_directory_uri() . '/assets/js/category-swiper.js', array('jquery', 'swiper-js'), '1.0.0', true);

	// Передаем данные в JavaScript
	wp_localize_script('category-swiper-js', 'catSwiperData', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('category_swiper_nonce')
	));
}
add_action('wp_enqueue_scripts', 'category_swiper_enqueue_scripts');

// Вспомогательная функция для работы с UTF-8
if (!function_exists('mb_ucfirst')) {
	function mb_ucfirst($str)
	{
		$fc = mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8'), 'UTF-8');
		return $fc . mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8');
	}
}

/**
 * Шорткод для отображения карусели записей категории
 * Использование: [category_swiper id="1"]
 */
function category_swiper_shortcode($atts)
{
	// Получаем атрибуты
	$atts = shortcode_atts(array(
		'id' => 0, // ID категории
		'excerpt_length' => 15, // Длина excerpt в словах
		'show_title' => true, // Показывать ли заголовок категории
	), $atts, 'category_swiper');

	// Генерируем уникальный ID для каждого слайдера
	$swiper_id = 'category-swiper-' . uniqid();

	// Получаем ID категории
	$category_id = intval($atts['id']);

	// Получаем полный объект категории
	$category = get_category($category_id);

	if ($category) {
		// Название категории
		$category_title = $category->name;

		// Описание категории
		$category_description = $category->description;

		// Ссылка на страницу категории
		$category_link = get_category_link($category_id);

		// Количество записей в категории
		$category_count = $category->count;

		// ID родительской категории (если есть)
		$parent_category_id = $category->parent;
	}

	// Получаем первые 12 записей категории
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 12,
		'cat' => $category_id,
		'orderby'        => 'rand', // добавлено для случайного порядка
	);

	$query = new WP_Query($args);

	// Начинаем буферизацию вывода
	ob_start();

	// Проверяем, есть ли записи
	if ($query->have_posts()) :
?>
		<div class="category-swiper-container mt-5" data-category="<?php echo esc_attr($atts['id']); ?>" data-swiper-id="<?php echo esc_attr($swiper_id); ?>" data-page="1" data-max="<?php echo esc_attr($query->max_num_pages); ?>" data-excerpt-length="<?php echo esc_attr($atts['excerpt_length']); ?>">

			<?php if ($atts['show_title'] && !empty($category_title)) : ?>
				<h2 class="display-3 mb-3">
					<a href="<?php echo esc_url($category_link); ?>" class="text-decoration-none text-black" target="_blank">
						<?php
						echo esc_html(
							mb_ucfirst(mb_strtolower($category_title))
						);
						?>
					</a>
				</h2>
			<?php endif; ?>

			<div id="<?php echo esc_attr($swiper_id); ?>" class="swiper">
				<div class="swiper-wrapper">
					<?php while ($query->have_posts()) : $query->the_post(); ?>
						<div class="swiper-slide h-100">
							<div class="card mb-3 shadow-sm h-100">
								<div class="row g-0 h-100">
									<div class="col-md-4 position-relative">
										<?php if (has_post_thumbnail()) : ?>
											<div class="post-thumbnail position-absolute top-0 start-0 w-100 h-100">
												<a href="<?php the_permalink(); ?>" class="d-block h-100">
													<?php
													the_post_thumbnail('thumbinal', [
														'class' => 'img-fluid rounded-start w-100 h-100 object-fit-cover',
														'style' => 'aspect-ratio: 4/3;'
													]);
													?>
												</a>
											</div>
										<?php endif; ?>
									</div>
									<div class="col-md-8">
										<div class="card-body d-flex flex-column h-100">
											<p class="card-title lead">
												<a href="<?php the_permalink(); ?>" class="text-decoration-none text-black">
													<?php the_title(); ?>
												</a>
											</p>
											<div class="card-text small flex-grow-1">
												<?php echo custom_excerpt_with_gradient(intval($atts['excerpt_length'])); ?>
											</div>
											<div class="post-read-more mt-auto">
												<a href="<?php the_permalink(); ?>" class="text-secondary card-link">Читать далее</a>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
				<div class="swiper-button-next bg-white rounded-circle shadow d-flex align-items-center justify-content-center"></div>
				<div class="swiper-button-prev bg-white rounded-circle shadow d-flex align-items-center justify-content-center"></div>
			</div>
		</div>
<?php
	endif;

	// Сбрасываем запрос
	wp_reset_postdata();

	// Возвращаем буферизированный вывод
	return ob_get_clean();
}
add_shortcode('category_swiper', 'category_swiper_shortcode');

/**
 * AJAX-обработчик для загрузки дополнительных постов
 */
function category_swiper_load_more()
{
	// Проверяем nonce
	if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'category_swiper_nonce')) {
		wp_send_json_error('Ошибка безопасности');
	}

	// Получаем параметры
	$category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
	$page = isset($_POST['page']) ? intval($_POST['page']) : 1;

	// Формируем запрос
	$args = array(
		'post_type' => 'post',
		'posts_per_page' => 12,
		'paged' => $page,
		'cat' => $category_id,
		'orderby'        => 'rand', // добавлено для случайного порядка
	);

	$query = new WP_Query($args);

	$slides = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();

			$thumbnail = '';
			if (has_post_thumbnail()) {
				$thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
			}

			$slides[] = array(
				'id' => get_the_ID(),
				'title' => get_the_title(),
				'excerpt' => get_the_excerpt(),
				'permalink' => get_permalink(),
				'thumbnail' => $thumbnail,
			);
		}

		wp_reset_postdata();

		wp_send_json_success(array(
			'slides' => $slides,
			'max_pages' => $query->max_num_pages,
		));
	} else {
		wp_send_json_error('Записей не найдено');
	}
}
add_action('wp_ajax_category_swiper_load_more', 'category_swiper_load_more');
add_action('wp_ajax_nopriv_category_swiper_load_more', 'category_swiper_load_more');


/**
 * Функция для обрезки текста до определенного количества слов
 * с добавлением класса для эффекта градиента
 */
function custom_excerpt_with_gradient($length = 15)
{
	$excerpt = get_the_excerpt();
	$words   = explode(' ', $excerpt);

	if (count($words) > $length) {
		$truncated = array_slice($words, 0, $length);

		return '<div class="truncated-excerpt"><span>' . implode(' ', $truncated) . '...</span><div class="excerpt-gradient"></div></div>';
	}

	return '<div class="truncated-excerpt"><span>' . $excerpt . '</span></div>';
}
