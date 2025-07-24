<?php
/**
 * Plugin Name: WP Dashboard Data Viewer
 * Description: A mini plugin that loads posts, users, and categories using Promise.all and displays them in the admin dashboard.
 * Version: 1.0
 * Author: Your Name
 */

add_action('admin_menu', function () {
    add_menu_page('Dashboard Viewer', 'Dashboard Viewer', 'manage_options', 'dashboard-viewer', 'render_dashboard_page');
});

function render_dashboard_page() {
    echo '<div class="wrap"><h1>Dashboard Data Viewer</h1><div id="wp-dashboard-data"></div></div>';
}

add_action('admin_enqueue_scripts', function ($hook) {
    if ($hook === 'toplevel_page_dashboard-viewer') {
        wp_enqueue_script('wp-dashboard-data-viewer', plugin_dir_url(__FILE__) . 'script.js', ['wp-api-fetch'], '1.0', true);
        wp_enqueue_style('wp-dashboard-data-style', plugin_dir_url(__FILE__) . 'style.css');
    }
});
