<?php
if (!defined('ABSPATH')) {
    exit;
}

class MultiLocationMap {

    public function __construct() {
        add_action('init', [$this, 'register_location_post_type']);
        add_action('add_meta_boxes', [$this, 'register_location_meta_boxes']);
        add_action('save_post', [$this, 'save_location_meta_boxes']);
        add_action('admin_menu', [$this, 'register_settings_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_shortcode('multi_location_map', [$this, 'render_map_shortcode']);
    }

    public function register_location_post_type() {
        $labels = [
            'name'               => 'Locations',
            'singular_name'      => 'Location',
            'menu_name'          => 'Locations',
            'name_admin_bar'     => 'Location',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Location',
            'edit_item'          => 'Edit Location',
            'new_item'           => 'New Location',
            'view_item'          => 'View Location',
            'search_items'       => 'Search Locations',
            'not_found'          => 'No locations found.',
            'not_found_in_trash' => 'No locations found in Trash.',
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-location',
            'supports'           => ['title', 'editor'],
            'has_archive'        => false,
            'rewrite'            => false,
            'show_in_rest'       => true,
        ];

        register_post_type('location', $args);
    }

    public function register_location_meta_boxes() {
        add_meta_box(
            'location_details',
            'Location Details',
            [$this, 'render_location_meta_box'],
            'location',
            'normal',
            'default'
        );
    }

    public function render_location_meta_box($post) {
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

        echo '<p><label for="custom_pin_image">Custom Pin Image:</label></p>';
        echo '<input type="text" name="custom_pin_image" id="custom_pin_image" value="' . esc_url($custom_pin_image) . '" class="regular-text" />';
        echo '<button type="button" class="button" id="upload_custom_pin_image_button">Upload Image</button>';
        echo '<img id="custom_pin_image_preview" src="' . esc_url($custom_pin_image) . '" style="max-width: 100px; margin-top: 10px; display: ' . ($custom_pin_image ? 'block' : 'none') . ';">';
    }

    public function save_location_meta_boxes($post_id) {
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

    public function register_settings_menu() {
        add_submenu_page(
            'edit.php?post_type=location',
            'Map Settings',
            'Settings',
            'manage_options',
            'map-settings',
            [$this, 'render_settings_page']
        );
    }

    public function render_settings_page() {
        echo '<div class="wrap"><h1>Map Settings</h1><form method="post" action="options.php">';
        settings_fields('map_settings_group');
        do_settings_sections('map_settings_group');
        echo '<table class="form-table">
                <tr>
                    <th><label for="map_api_key">API Key</label></th>
                    <td><input name="map_api_key" id="map_api_key" type="text" value="' . esc_attr(get_option('map_api_key')) . '" class="regular-text"></td>
                </tr>
              </table>
              <p class="submit"><input type="submit" class="button-primary" value="Save Changes"></p>
              </form></div>';
    }
}
