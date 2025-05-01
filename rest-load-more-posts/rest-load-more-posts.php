<?php
/*
Plugin Name: REST Load More Posts
Description: Display posts with a "Load More" button using the REST API.
Version: 1.0
Author: Your Name
*/

defined('ABSPATH') || exit;

class REST_Load_More_Posts {

    public function __construct() {
        add_shortcode('rest_load_more_posts', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            'rest-load-more-script',
            plugin_dir_url(__FILE__) . 'rest-load-more.js',
            ['jquery'],
            null,
            true
        );

        wp_localize_script('rest-load-more-script', 'RestLoadMoreSettings', [
            'rest_url' => rest_url('wp/v2/posts'),
            'nonce'    => wp_create_nonce('wp_rest'),
        ]);

        wp_enqueue_style(
            'rest-load-more-style',
            plugin_dir_url(__FILE__) . 'style.css'
        );
    }

    public function render_shortcode() {
        ob_start();
        ?>
        <div id="rest-posts-container"></div>
        <button id="rest-load-more">Load More</button>
        <?php
        return ob_get_clean();
    }
}

new REST_Load_More_Posts();
