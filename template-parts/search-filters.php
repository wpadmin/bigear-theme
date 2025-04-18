<div class="search-filters-container my-4">
    <form id="ajax-search-form" class="row g-3">
        <!-- Search input -->
        <div class="col-md-6">
            <div class="form-floating">
                <input type="text" class="form-control" id="search-input" placeholder="Search..." autocomplete="off">
                <label for="search-input">Search</label>
                <div class="search-suggestions dropdown-menu w-100" id="search-suggestions"></div>
            </div>
        </div>

        <!-- Author tags select -->
        <div class="col-md-3">
            <div class="form-floating">
                <select class="form-select" id="author-select">
                    <option value="">All Authors</option>
                    <?php
                    $authors = get_users(['role__in' => ['author', 'editor', 'administrator']]);
                    foreach($authors as $author) {
                        echo sprintf(
                            '<option value="%s">%s</option>',
                            esc_attr($author->ID),
                            esc_html($author->display_name)
                        );
                    }
                    ?>
                </select>
                <label for="author-select">Author</label>
            </div>
        </div>

        <!-- Year select -->
        <div class="col-md-2">
            <div class="form-floating">
                <select class="form-select" id="year-select">
                    <option value="">All Years</option>
                    <?php
                    $years = $wpdb->get_col(
                        "SELECT DISTINCT YEAR(post_date) as year 
                        FROM {$wpdb->posts} 
                        WHERE post_type = 'post' 
                        AND post_status = 'publish' 
                        ORDER BY year DESC"
                    );
                    foreach($years as $year) {
                        echo sprintf(
                            '<option value="%s">%s</option>',
                            esc_attr($year),
                            esc_html($year)
                        );
                    }
                    ?>
                </select>
                <label for="year-select">Year</label>
            </div>
        </div>

        <!-- Search button -->
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary h-100 w-100">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <!-- Results container -->
    <div id="search-results" class="mt-4"></div>
</div>