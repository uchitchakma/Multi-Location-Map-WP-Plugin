<?php
namespace MultiLocationMap;
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class UC_LocationMetaBox {

        // Add Meta Boxes for Location Search and Content
        public static function register_meta_boxes() {
            add_meta_box(
                'location_details',
                'Location Details',
                [__CLASS__, 'render_location_meta_box'],
                'location',
                'normal',
                'default'
            );
        }

    // Render Meta Box HTML
    public static function render_location_meta_box($post) {
        $latitude = get_post_meta($post->ID, '_latitude', true);
        $longitude = get_post_meta($post->ID, '_longitude', true);
        $custom_pin_image = get_post_meta($post->ID, '_custom_pin_image', true);
    
        wp_nonce_field('save_location_details', 'location_details_nonce');
    
        echo '<p><label for="location_name">Search Location:</label></p>';
        echo '<input type="text" id="location_name" class="regular-text" placeholder="Search for a location" />';
    
        echo '<p><label for="latitude">Latitude:</label></p>';
        echo '<input type="text" name="latitude" id="latitude" value="' . esc_attr($latitude) . '" class="regular-text" readonly />';
    
        echo '<p><label for="longitude">Longitude:</label></p>';
        echo '<input type="text" name="longitude" id="longitude" value="' . esc_attr($longitude) . '" class="regular-text" readonly />';
    
        // Add a custom field for the pin image
        echo '<p><label for="custom_pin_image">Custom Pin Image:</label></p>';
        echo '<input type="text" name="custom_pin_image" id="custom_pin_image" value="' . esc_url($custom_pin_image) . '" class="regular-text" />';
        echo '<button type="button" class="button" id="upload_custom_pin_image_button">Upload Image</button>';
        echo '<img id="custom_pin_image_preview" src="' . esc_url($custom_pin_image) . '" style="max-width: 100px; margin-top: 10px; display: ' . ($custom_pin_image ? 'block' : 'none') . ';">';
    }

    // Save Meta Box Data
    public static function save_meta_boxes($post_id) {
        if (!isset($_POST['location_details_nonce']) || !wp_verify_nonce($_POST['location_details_nonce'], 'save_location_details')) {
            return;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    
        if (isset($_POST['latitude'])) {
            update_post_meta($post_id, '_latitude', sanitize_text_field($_POST['latitude']));
        }
    
        if (isset($_POST['longitude'])) {
            update_post_meta($post_id, '_longitude', sanitize_text_field($_POST['longitude']));
        }
    
        if (isset($_POST['custom_pin_image'])) {
            update_post_meta($post_id, '_custom_pin_image', esc_url_raw($_POST['custom_pin_image']));
        }
    }
}