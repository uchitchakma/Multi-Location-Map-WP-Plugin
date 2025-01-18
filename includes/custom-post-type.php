<?php

// Register custom post type for locations
function mldm_register_custom_post_type() {
    register_post_type('mldm_location', [
        'labels' => [
            'name' => __('Locations', 'mldm'),
            'singular_name' => __('Location', 'mldm'),
        ],
        'public' => true,
        'show_ui' => true,
        'supports' => ['title', 'editor', 'thumbnail'],
        'menu_icon' => 'dashicons-location-alt',
    ]);
}
add_action('init', 'mldm_register_custom_post_type');

// Add meta boxes
function mldm_add_meta_boxes() {
    add_meta_box('mldm_location_meta', 'Location Details', 'mldm_location_meta_callback', 'mldm_location', 'normal', 'default');
}
add_action('add_meta_boxes', 'mldm_add_meta_boxes');

// Meta box callback
function mldm_location_meta_callback($post) {
    wp_nonce_field('mldm_save_meta_data', 'mldm_nonce');

    $lat = get_post_meta($post->ID, '_mldm_lat', true);
    $lng = get_post_meta($post->ID, '_mldm_lng', true);
    $pin = get_post_meta($post->ID, '_mldm_pin', true);
    ?>
    <label for="mldm_location_search">Search Location:</label>
    <input type="text" id="mldm_location_search" placeholder="Search location" style="width: 100%;"><br><br>

    <label for="mldm_lat">Latitude:</label>
    <input type="text" id="mldm_lat" name="mldm_lat" value="<?php echo esc_attr($lat); ?>" readonly style="width: 100%;"><br><br>

    <label for="mldm_lng">Longitude:</label>
    <input type="text" id="mldm_lng" name="mldm_lng" value="<?php echo esc_attr($lng); ?>" readonly style="width: 100%;"><br><br>

    <label for="mldm_pin">Custom Pin URL:</label>
    <input type="text" id="mldm_pin" name="mldm_pin" value="<?php echo esc_url($pin); ?>" style="width: 100%;"><br><br>
    <p>Optional: Upload an image and copy its URL here.</p>
    <?php
}

// Save meta box data
function mldm_save_meta_data($post_id) {
    if (!isset($_POST['mldm_nonce']) || !wp_verify_nonce($_POST['mldm_nonce'], 'mldm_save_meta_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['mldm_lat'])) {
        update_post_meta($post_id, '_mldm_lat', sanitize_text_field($_POST['mldm_lat']));
    }
    if (isset($_POST['mldm_lng'])) {
        update_post_meta($post_id, '_mldm_lng', sanitize_text_field($_POST['mldm_lng']));
    }
    if (isset($_POST['mldm_pin'])) {
        update_post_meta($post_id, '_mldm_pin', esc_url_raw($_POST['mldm_pin']));
    }
}
add_action('save_post', 'mldm_save_meta_data');
