<?php
// Register Ajax handlers
add_action('wp_ajax_bigear_ajax_search', 'bigear_handle_ajax_search');
add_action('wp_ajax_nopriv_bigear_ajax_search', 'bigear_handle_ajax_search');

// Enqueue necessary scripts
function bigear_enqueue_search_scripts() {
    wp_enqueue_script(
        'bigear-ajax-search',
        get_template_directory_uri() . '/assets/js/ajax-search.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_localize_script('bigear-ajax-search', 'bigearAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('bigear_search_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'bigear_enqueue_search_scripts');

// Handle Ajax search requests
function bigear_handle_ajax_search() {
    check_ajax_referer('bigear_search_nonce', 'security');

    $search = sanitize_text_field($_POST['search']);
    $author = intval($_POST['author']);
    $year = intval($_POST['year']);
    $suggestions = isset($_POST['suggestions']) ? filter_var($_POST['suggestions'], FILTER_VALIDATE_BOOLEAN) : false;

    $args = array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => $suggestions ? 5 : 10,
        's' => $search
    );

    if ($author) {
        $args['author'] = $author;
    }

    if ($year) {
        $args['date_query'] = array(
            array(
                'year' => $year
            )
        );
    }

    $query = new WP_Query($args);

    if ($suggestions) {
        $suggestions_array = array();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $suggestions_array[] = get_the_title();
            }
        }
        wp_send_json(array('suggestions' => $suggestions_array));
    } else {
        ob_start();
        if ($query->have_posts()) {
            echo '<div class="row row-cols-1 row-cols-md-2 g-4">';
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <div class="col">
                    <div class="card h-100">
                        <?php if (has_post_thumbnail()): ?>
                            <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?php the_title_attribute(); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title"><?php the_title(); ?></h5>
                            <p class="card-text"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted"><?php echo get_the_date(); ?></small>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            
            // Pagination
            $total_pages = $query->max_num_pages;
            if ($total_pages > 1) {
                echo '<div class="pagination justify-content-center mt-4">';
                echo paginate_links(array(
                    'total' => $total_pages,
                    'current' => max(1, get_query_var('paged')),
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                    'type' => 'list',
                    'end_size' => 3,
                    'mid_size' => 3
                ));
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-info">No posts found matching your criteria.</div>';
        }
        wp_reset_postdata();
        
        wp_send_json(array('html' => ob_get_clean()));
    }
}