<?php
namespace MultiLocationMap;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class UC_LocationSettings {

    // Register Settings Menu
    public static function register_settings_menu() {
        add_submenu_page(
            'edit.php?post_type=location',
            'Map Settings',
            'Settings',
            'manage_options',
            'map-settings',
            [__CLASS__, 'render_settings_page']
        );
    }
    public static function register_settings() {
        register_setting('map_settings_group', 'map_api_key');
        register_setting('map_settings_group', 'map_pin_image');
        register_setting('map_settings_group', 'map_pin_size');
        register_setting('map_settings_group', 'map_height_desktop');
        register_setting('map_settings_group', 'map_height_tablet');
        register_setting('map_settings_group', 'map_height_mobile');
    }

    public static function render_settings_page() {
        echo '<div class="wrap"><h1>Map Settings</h1><form method="post" action="options.php">';
        settings_fields('map_settings_group');
        do_settings_sections('map_settings_group');
        echo '<table class="form-table">
            <tr>
                <th><label for="map_api_key">API Key</label></th>
                <td><input name="map_api_key" id="map_api_key" type="text" value="' . esc_attr(get_option('map_api_key')) . '" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="map_pin_image">Pin Image</label></th>
                <td>
                    <input name="map_pin_image" id="map_pin_image" type="hidden" value="' . esc_attr(get_option('map_pin_image')) . '">
                    <button type="button" class="button" id="upload_pin_image_button">Select Image</button>
                    <img id="pin_image_preview" src="' . esc_attr(get_option('map_pin_image')) . '" style="max-width: 100px; margin-top: 10px; display: ' . (get_option('map_pin_image') ? 'block' : 'none') . ';">
                </td>
            </tr>
            <tr>
                <th><label for="map_pin_size">Pin Size</label></th>
                <td><input name="map_pin_size" id="map_pin_size" type="number" value="' . esc_attr(get_option('map_pin_size')) . '" class="small-text"> px</td>
            </tr>
            <tr>
                <th><label for="map_height_desktop">Map Height (Desktop)</label></th>
                <td><input name="map_height_desktop" id="map_height_desktop" type="number" value="' . esc_attr(get_option('map_height_desktop', 500)) . '" class="small-text"> px</td>
            </tr>
            <tr>
                <th><label for="map_height_tablet">Map Height (Tablet)</label></th>
                <td><input name="map_height_tablet" id="map_height_tablet" type="number" value="' . esc_attr(get_option('map_height_tablet', 400)) . '" class="small-text"> px</td>
            </tr>
            <tr>
                <th><label for="map_height_mobile">Map Height (Mobile)</label></th>
                <td><input name="map_height_mobile" id="map_height_mobile" type="number" value="' . esc_attr(get_option('map_height_mobile', 300)) . '" class="small-text"> px</td>
            </tr>
        </table>
        <p class="submit"><input type="submit" class="button-primary" value="Save Changes"></p>
        </form></div>';
                // Add Shortcode Display Section
                echo '<h2>Shortcode</h2>';
                echo '<p>Use the following shortcode to display the map on your site:</p>';
                echo '<div style="background: #f1f1f1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; max-width: 600px;">';
                echo '<code>[multi_location_map]</code>';
                echo '</div>';
                echo '</div>';
    }
}