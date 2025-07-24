<?php
/*
Plugin Name: Contact Form with wp_remote_post
Description: A simple contact form that sends data to an external API via wp_remote_post().
Version: 1.0
Author: Eleas Kanchon
*/

defined('ABSPATH') || exit;

class Contact_Form_Remote_API {

    public function __construct() {
        add_shortcode('contact_form_api', [$this, 'render_form']);
        add_action('init', [$this, 'handle_form']);
    }

    public function render_form() {
        ob_start();
        ?>

        <?php if (isset($_GET['sent']) && $_GET['sent'] == '1') : ?>
            <div style="color: green;">Form sent successfully!</div>
        <?php elseif (isset($_GET['sent']) && $_GET['sent'] == '0') : ?>
            <div style="color: red;">Something went wrong.</div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="cf_name" placeholder="Your Name" required><br>
            <input type="email" name="cf_email" placeholder="Your Email" required><br>
            <textarea name="cf_message" placeholder="Your Message" required></textarea><br>
            <input type="submit" name="cf_submit" value="Send">
            <?php wp_nonce_field('cf_submit_action', 'cf_nonce'); ?>
        </form>

        <?php
        return ob_get_clean();
    }

    public function handle_form() {
        if (!isset($_POST['cf_submit'])) {
            return;
        }

        if (!isset($_POST['cf_nonce']) || !wp_verify_nonce($_POST['cf_nonce'], 'cf_submit_action')) {
            wp_die('Security check failed');
        }

        $name = sanitize_text_field($_POST['cf_name']);
        $email = sanitize_email($_POST['cf_email']);
        $message = sanitize_textarea_field($_POST['cf_message']);

        $api_url = 'https://jsonplaceholder.typicode.com/posts'; // Fake test API

        $response = wp_remote_post($api_url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                'name'    => $name,
                'email'   => $email,
                'message' => $message
            ])
        ]);

        if (is_wp_error($response)) {
            wp_redirect(add_query_arg('sent', '0', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('sent', '1', wp_get_referer()));
        }

        exit;
    }
}

new Contact_Form_Remote_API();
