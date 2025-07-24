<?php
/**
 * Plugin Name: WP Frontend Data Viewer
 * Description: A mini plugin that shows posts, users, and categories using Promise.all in the frontend.
 * Version: 1.1
 * Author: Your Name
 */

// Register shortcode
add_shortcode('frontend_data_viewer', 'wpfdv_render_shortcode');

function wpfdv_render_shortcode() {
    ob_start();
    ?>
    <div id="wp-frontend-post-controls">
        <label>Sort by:
            <select id="sort-by">
                <option value="title">Title</option>
                <option value="date">Date</option>
            </select>
        </label>
        <label>Filter by Category:
            <select id="filter-category">
                <option value="">All</option>
            </select>
        </label>
    </div>
    <div id="wp-frontend-posts"></div>
    <?php
    return ob_get_clean();
}


// Enqueue scripts
add_action('wp_enqueue_scripts', function () {
    if (is_singular() && has_shortcode(get_post()->post_content, 'frontend_data_viewer')) {
        wp_enqueue_script('wpfdv-script', plugin_dir_url(__FILE__) . 'script.js', ['wp-api-fetch'], '1.1', true);
        wp_enqueue_style('wpfdv-style', plugin_dir_url(__FILE__) . 'style.css');
    }
});
