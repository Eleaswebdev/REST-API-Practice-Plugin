<?php
/**
 * Plugin Name: MyPlugin Custom API
 * Description: Example of creating custom REST API endpoints
 * Version: 1.0
 * Author: Your Name
 */

add_action('rest_api_init', function() {
  
  // GET: Fetch user profile
  register_rest_route('myplugin/v1', '/user-profile', array(
    'methods' => 'GET',
    'callback' => 'myplugin_get_user_profile',
    'permission_callback' => '__return_true'

  ));

  // POST: Save data
  register_rest_route('myplugin/v1', '/save-data', array(
    'methods' => 'POST',
    'callback' => 'myplugin_save_data',
   'permission_callback' => '__return_true'
  ));

});

// Function to get user profile
function myplugin_get_user_profile(WP_REST_Request $request) {
    return array(
        'message' => 'This is a public API!',
        'site_name' => get_bloginfo('name'),
        'site_url' => get_bloginfo('url'),
      );
}

// Function to save data
function myplugin_save_data(WP_REST_Request $request) {
  $params = $request->get_params(); // get POSTed data

  // Here you can sanitize, validate and save data
  if (isset($params['my_field'])) {
    return array(
      'success' => true,
      'message' => 'Data saved: ' . sanitize_text_field($params['my_field'])
    );
  } else {
    return new WP_Error('missing_field', 'my_field is required', array('status' => 400));
  }
}

