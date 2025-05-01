<?php
/**
 * Plugin Name: My Plugin Custom API Endpoint
 * Description: A plugin with settings, REST API, and frontend support.
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

class My_Plugin_API {
    private $option_name = 'my_plugin_api_settings';
    private $fields = ['field1', 'field2', 'field3', 'field4', 'field5'];

    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('rest_api_init', [$this, 'allow_cors_headers']);
    }

    public function add_settings_page() {
        add_menu_page('My Plugin Settings', 'My Plugin', 'manage_options', 'my-plugin-settings', [$this, 'render_settings_page']);
    }

    public function register_settings() {
        register_setting('my_plugin_api_group', $this->option_name);
        add_settings_section('my_plugin_section', 'Main Settings', null, 'my-plugin-settings');

        foreach ($this->fields as $field) {
            add_settings_field($field, ucfirst($field), [$this, 'field_callback'], 'my-plugin-settings', 'my_plugin_section', ['id' => $field]);
        }
    }

    public function field_callback($args) {
        $options = get_option($this->option_name);
        $value = isset($options[$args['id']]) ? $options[$args['id']] : '';
        echo '<input type="text" name="' . $this->option_name . '[' . $args['id'] . ']" value="' . esc_attr($value) . '" />';
    }

    public function render_settings_page() {
        echo '<div class="wrap"><h1>My Plugin Settings</h1>';
        echo '<form method="post" action="options.php">';
        settings_fields('my_plugin_api_group');
        do_settings_sections('my-plugin-settings');
        submit_button();
        echo '</form></div>';
    }

    public function register_rest_routes() {
        register_rest_route('my-plugin/v1', '/settings', [
            'methods' => 'GET',
            'callback' => [$this, 'get_settings'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('my-plugin/v1', '/settings', [
            'methods' => 'POST',
            'callback' => [$this, 'create_settings'],
            'permission_callback' => [$this, 'can_edit'],
        ]);

        register_rest_route('my-plugin/v1', '/settings', [
            'methods' => 'PUT',
            'callback' => [$this, 'update_settings'],
            'permission_callback' => [$this, 'can_edit'],
        ]);

        register_rest_route('my-plugin/v1', '/settings', [
            'methods' => 'DELETE',
            'callback' => [$this, 'delete_settings'],
            'permission_callback' => [$this, 'can_edit'],
        ]);
    }

    public function get_settings() {
        return get_option($this->option_name);
    }

    public function create_settings($request) {
        $data = $request->get_json_params();
        update_option($this->option_name, $data);
        return ['status' => 'created', 'data' => $data];
    }

    public function update_settings($request) {
        $data = $request->get_json_params();
        update_option($this->option_name, $data);
        return ['status' => 'updated', 'data' => $data];
    }

    public function delete_settings() {
        delete_option($this->option_name);
        return ['status' => 'deleted'];
    }

    public function can_edit() {
        return current_user_can('manage_options');
    }

    public function allow_cors_headers() {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
    }
}

new My_Plugin_API();
