<?php
class Bootstrap_Categories_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'bootstrap_categories_widget',
            __('Bootstrap Categories Select', 'textdomain'),
            array(
                'description' => __('A Bootstrap styled categories dropdown', 'textdomain'),
            )
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        $this->render_categories_select($instance);

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : __('Categories', 'textdomain');
        $show_count = isset($instance['show_count']) ? (bool) $instance['show_count'] : false;
        $show_hierarchical = isset($instance['show_hierarchical']) ? (bool) $instance['show_hierarchical'] : false;
        $show_empty = isset($instance['show_empty']) ? (bool) $instance['show_empty'] : true;
?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'textdomain'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_count')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_count')); ?>" <?php checked($show_count); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('show_count')); ?>"><?php _e('Show post counts', 'textdomain'); ?></label>
        </p>
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_hierarchical')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_hierarchical')); ?>" <?php checked($show_hierarchical); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('show_hierarchical')); ?>"><?php _e('Show hierarchy', 'textdomain'); ?></label>
        </p>
        <p>
            <input type="checkbox" class="checkbox" id="<?php echo esc_attr($this->get_field_id('show_empty')); ?>"
                name="<?php echo esc_attr($this->get_field_name('show_empty')); ?>" <?php checked($show_empty); ?> />
            <label for="<?php echo esc_attr($this->get_field_id('show_empty')); ?>"><?php _e('Show empty categories', 'textdomain'); ?></label>
        </p>
    <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_count'] = isset($new_instance['show_count']) ? (bool) $new_instance['show_count'] : false;
        $instance['show_hierarchical'] = isset($new_instance['show_hierarchical']) ? (bool) $new_instance['show_hierarchical'] : false;
        $instance['show_empty'] = isset($new_instance['show_empty']) ? (bool) $new_instance['show_empty'] : false;

        return $instance;
    }

    private function render_categories_select($instance)
    {
        $show_count = isset($instance['show_count']) ? $instance['show_count'] : false;
        $show_hierarchical = isset($instance['show_hierarchical']) ? $instance['show_hierarchical'] : false;
        $show_empty = isset($instance['show_empty']) ? $instance['show_empty'] : true;

        $categories = get_categories(array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => !$show_empty,
            'hierarchical' => $show_hierarchical,
        ));

        if (empty($categories)) {
            return;
        }

        $current_category = get_queried_object();
        $current_cat_id = is_category() ? $current_category->term_id : 0;
    ?>

        <div class="bootstrap-categories-widget">
            <div class="mb-3">
                <select class="form-select form-select-lg" id="categoriesSelect" onchange="location = this.value;">
                    <option value="<?php echo esc_url(home_url('/')); ?>" <?php selected($current_cat_id, 0); ?>>
                        <?php _e('All Categories', 'textdomain'); ?>
                    </option>
                    <?php
                    if ($show_hierarchical) {
                        $this->render_hierarchical_options($categories, $current_cat_id, $show_count);
                    } else {
                        foreach ($categories as $category) {
                            $count_text = $show_count ? ' (' . $category->count . ')' : '';
                    ?>
                            <option value="<?php echo esc_url(get_category_link($category->term_id)); ?>"
                                <?php selected($current_cat_id, $category->term_id); ?>>
                                <?php echo esc_html($category->name . $count_text); ?>
                            </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Альтернативный вариант с иконками -->
            <div class="d-none" id="alternativeSelect">
                <select class="form-select" aria-label="Select category">
                    <option selected>
                        <i class="bi bi-folder me-2"></i><?php _e('Choose category...', 'textdomain'); ?>
                    </option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo esc_url(get_category_link($category->term_id)); ?>">
                            <i class="bi bi-tag me-2"></i><?php echo esc_html($category->name); ?>
                            <?php if ($show_count): ?>
                                <span class="badge bg-secondary ms-2"><?php echo $category->count; ?></span>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <?php
    }

    private function render_hierarchical_options($categories, $current_cat_id, $show_count, $parent = 0, $level = 0)
    {
        $children = array_filter($categories, function ($cat) use ($parent) {
            return $cat->parent == $parent;
        });

        foreach ($children as $category) {
            $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
            $count_text = $show_count ? ' (' . $category->count . ')' : '';
        ?>
            <option value="<?php echo esc_url(get_category_link($category->term_id)); ?>"
                <?php selected($current_cat_id, $category->term_id); ?>>
                <?php echo $indent . esc_html($category->name . $count_text); ?>
            </option>
<?php
            // Рекурсивно добавляем дочерние категории
            $this->render_hierarchical_options($categories, $current_cat_id, $show_count, $category->term_id, $level + 1);
        }
    }
}

// Регистрируем виджет
function register_bootstrap_categories_widget()
{
    register_widget('Bootstrap_Categories_Widget');
}
add_action('widgets_init', 'register_bootstrap_categories_widget');
