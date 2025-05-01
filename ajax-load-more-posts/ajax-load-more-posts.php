<?php
/*
Plugin Name: jQuery AJAX Load More Posts
Description: Load posts with jQuery-style admin-ajax call and shortcode.
Version: 1.0
Author: Your Name
*/

defined('ABSPATH') || exit;

class JQuery_AJAX_Load_More {

    public function __construct() {
        add_shortcode('jquery_ajax_load_more', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_nopriv_jquery_load_more_posts', [$this, 'handle_ajax']);
        add_action('wp_ajax_jquery_load_more_posts', [$this, 'handle_ajax']);
    }

    public function enqueue_assets() {
        wp_enqueue_script('jquery');
        wp_enqueue_script(
            'jquery-ajax-load-more',
            plugin_dir_url(__FILE__) . 'jquery-ajax-load-more.js',
            ['jquery'],
            null,
            true
        );

        wp_localize_script('jquery-ajax-load-more', 'ajaxurl', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('jquery_ajax_nonce'),
        ]);

        wp_enqueue_style(
            'jquery-ajax-load-more-style',
            plugin_dir_url(__FILE__) . 'style.css'
        );
    }

    public function render_shortcode() {
        ob_start(); ?>
        <div id="jquery-posts-container"></div>
        <button id="jquery-load-more">Load More</button>
        <?php return ob_get_clean();
    }

    public function handle_ajax() {
        check_ajax_referer('jquery_ajax_nonce', 'nonce');

        $paged = isset($_POST['page']) ? intval($_POST['page']) : 1;

        $query = new WP_Query([
            'post_type'      => 'post',
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'paged'          => $paged,
        ]);

        $results = [];

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $results[] = [
                    'title'   => get_the_title(),
                    'excerpt' => get_the_excerpt(),
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json_success($results);
    }
}

new JQuery_AJAX_Load_More();
