<?php
/**
 * Plugin Name: Multi Location Map
 * Description: A plugin to display a multi-location map with popups and customizable settings.
 * Version: 1.1
 * Author: Your Name
 */

// Exit if accessed directly
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

    public function register_elementor_support() {
        add_post_type_support('location', 'elementor');
    }


    // Register Custom Post Type
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
            'supports'           => ['title', 'editor'], // Enable editor support
            'has_archive'        => false,
            'rewrite'            => false,
            'show_in_rest'       => true, // Required for Gutenberg
        ];
    
        register_post_type('location', $args);
    }
    
    
    

    // Add Meta Boxes for Location Search and Content
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
    

    // Render Meta Box HTML
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
    
        // Add a custom field for the pin image
        echo '<p><label for="custom_pin_image">Custom Pin Image:</label></p>';
        echo '<input type="text" name="custom_pin_image" id="custom_pin_image" value="' . esc_url($custom_pin_image) . '" class="regular-text" />';
        echo '<button type="button" class="button" id="upload_custom_pin_image_button">Upload Image</button>';
        echo '<img id="custom_pin_image_preview" src="' . esc_url($custom_pin_image) . '" style="max-width: 100px; margin-top: 10px; display: ' . ($custom_pin_image ? 'block' : 'none') . ';">';
    }
    
    
    

    // Save Meta Box Data
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
    

    // Enqueue Scripts for Admin
    public function admin_enqueue_scripts($hook) {
        $api_key = esc_attr(get_option('map_api_key'));
    
        // Check if on the Map Settings page
        if ($hook === 'location_page_map-settings') { 
            wp_enqueue_media(); // Enqueue WordPress media uploader
            wp_enqueue_script('map-settings-script', plugin_dir_url(__FILE__) . 'assets/js/map-settings.js', ['jquery'], null, true);
        }
    
        // Enqueue autocomplete script for the Location post type editor
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            global $post_type;
            if ('location' === $post_type) {
                wp_enqueue_script('google-maps-autocomplete', "https://maps.googleapis.com/maps/api/js?key=$api_key&libraries=places", [], null, true);
                wp_enqueue_script('location-admin-scripts', plugin_dir_url(__FILE__) . 'assets/js/admin-location.js', ['jquery', 'google-maps-autocomplete'], null, true);
            }
        }
    }
    

    // Enqueue Frontend Scripts
    public function enqueue_scripts() {
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr(get_option('map_api_key')) . '&libraries=places', [], null, true);
    }

    // Register Settings Menu
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

    public function register_settings() {
        register_setting('map_settings_group', 'map_api_key');
        register_setting('map_settings_group', 'map_pin_image');
        register_setting('map_settings_group', 'map_pin_size');
        register_setting('map_settings_group', 'map_height_desktop');
        register_setting('map_settings_group', 'map_height_tablet');
        register_setting('map_settings_group', 'map_height_mobile');
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
    }
    
    
    

    public function render_map_shortcode() {
        $args = [
            'post_type'      => 'location',
            'posts_per_page' => -1,
        ];
    
        $locations = get_posts($args);
    
        $map_data = [];
        $default_pin_image = esc_url(get_option('map_pin_image'));
        $pin_size = intval(get_option('map_pin_size'));
        $map_height_desktop = esc_attr(get_option('map_height_desktop', 500));
        $map_height_tablet = esc_attr(get_option('map_height_tablet', 400));
        $map_height_mobile = esc_attr(get_option('map_height_mobile', 300));
    
        foreach ($locations as $location) {
            $custom_pin_image = get_post_meta($location->ID, '_custom_pin_image', true);
            $map_data[] = [
                'title'     => $location->post_title,
                'latitude'  => get_post_meta($location->ID, '_latitude', true),
                'longitude' => get_post_meta($location->ID, '_longitude', true),
                'content'   => apply_filters('the_content', $location->post_content),
                'pin_image' => $custom_pin_image ? $custom_pin_image : $default_pin_image, // Use custom pin or default
            ];
        }
    
        ob_start();
        echo '<style>
            #map {
                width: 100%;
            }
            @media (max-width: 768px) {
                #map {
                    height: ' . $map_height_mobile . 'px;
                }
            }
            @media (min-width: 769px) and (max-width: 1024px) {
                #map {
                    height: ' . $map_height_tablet . 'px;
                }
            }
            @media (min-width: 1025px) {
                #map {
                    height: ' . $map_height_desktop . 'px;
                }
            }
        </style>';
        echo '<div id="map"></div>';
        echo '<script>
            function initMap() {
                const map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 5,
                    center: { lat: 20.5937, lng: 78.9629 },
                });
                const bounds = new google.maps.LatLngBounds();
                const locations = ' . json_encode($map_data) . ';
                let activeInfoWindow = null; // Track the currently active InfoWindow
    
                locations.forEach(location => {
                    const position = { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) };
                    const pinImage = {
                        url: location.pin_image,
                        scaledSize: new google.maps.Size(' . $pin_size . ', ' . $pin_size . ')
                    };
                    const marker = new google.maps.Marker({
                        position: position,
                        map,
                        title: location.title,
                        icon: pinImage,
                    });
                    bounds.extend(position);
                    const infowindow = new google.maps.InfoWindow({
                        content: location.content,
                    });
    
                    marker.addListener("click", () => {
                        if (activeInfoWindow) {
                            activeInfoWindow.close(); // Close the currently open InfoWindow
                        }
                        infowindow.open(map, marker); // Open the clicked marker\'s InfoWindow
                        activeInfoWindow = infowindow; // Set this InfoWindow as active
                    });
                });
                map.fitBounds(bounds);
            }
        </script>';
        echo '<script src="https://maps.googleapis.com/maps/api/js?key=' . esc_attr(get_option('map_api_key')) . '&callback=initMap" async defer></script>';
        return ob_get_clean();
    }
    
    
    
    
    
    
}

new MultiLocationMap();
