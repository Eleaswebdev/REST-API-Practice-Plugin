<?php
/*
Plugin Name: Sync Post to Remote Site
Description: Sends new posts to a remote WordPress site.
Version: 1.0
*/

add_action('save_post', 'sync_post_to_remote_site', 10, 3);

function sync_post_to_remote_site($post_ID, $post, $update) {
    if ($update || wp_is_post_autosave($post_ID) || wp_is_post_revision($post_ID)) return;
    if ($post->post_status !== 'publish') return;

    $remote_url = 'http://rest-remote-posthttp.local/wp-json/remote-sync/v1/create-post';

    $data = [
        'title'   => 'Test111',
        'content' => $post->post_content,
        'status'  => $post->post_status,
        'key'     => 'secret123'
    ];

    $response = wp_remote_post($remote_url, [
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => json_encode($data),
        'timeout' => 10,
    ]);

    if (is_wp_error($response)) {
        error_log('Remote Post Sync Error: ' . $response->get_error_message());
    } else {
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        error_log("Remote Post Sync Response Code: $code");
        error_log("Remote Post Sync Response Body: $body");
    }
}


add_action('admin_menu', function () {
    add_menu_page('Test Sync', 'Test Sync', 'manage_options', 'test-sync', 'manual_sync_page');
});

function manual_sync_page() {
    if (isset($_POST['run_test'])) {
        $post_data = [
            'title'   => 'Manual Sync Test ' . wp_rand(),
            'content' => 'Content from manual test.',
            'status'  => 'publish',
            'key'     => 'secret123',
        ];

        $response = wp_remote_post('http://rest-remote-posthttp.local/wp-json/remote-sync/v1/create-post', [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode($post_data),
        ]);

        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }

    echo '<form method="post"><button name="run_test">Send Test Post</button></form>';
}
