<?php
/*
Plugin Name: Fetch Remote Posts Using wp_remote_get
Description: Fetches posts from an external API using wp_remote_get() and displays via frontend using fetch().
Version: 1.0
Author: Eleas Kanchon
*/

defined('ABSPATH') || exit;

class Fetch_Remote_Posts {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_api']);
        add_shortcode('fetch_remote_posts', [$this, 'render_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    // Register custom REST endpoint
    public function register_api() {
        register_rest_route('custom/v1', '/external-posts', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_external_posts'],
            'permission_callback' => '__return_true'
        ]);
    }

    // Use wp_remote_get to fetch data server-side
    public function get_external_posts() {
        $response = wp_remote_get('https://jsonplaceholder.typicode.com/posts');

        if (is_wp_error($response)) {
            return new WP_Error('api_error', 'Failed to fetch external data', ['status' => 500]);
        }

        $body = wp_remote_retrieve_body($response);
        $posts = json_decode($body, true);

        return array_slice($posts, 0, 5); // Limit to 5 posts for demo
    }

    // Enqueue frontend JS and localize
    public function enqueue_scripts() {
        wp_enqueue_script(
            'fetch-remote-posts-js',
            plugin_dir_url(__FILE__) . 'fetch-remote-posts.js',
            [],
            null,
            true
        );

        wp_localize_script('fetch-remote-posts-js', 'fetchPosts', [
            'endpoint' => rest_url('custom/v1/external-posts')
        ]);
    }

    // Shortcode to trigger the JS
    public function render_shortcode() {
        return '<div id="external-posts-container"><button id="load-external-posts">Load Remote Posts</button></div>';
    }
}

new Fetch_Remote_Posts();
