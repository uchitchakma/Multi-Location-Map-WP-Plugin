<?php
namespace MultiLocationMap;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class UC_LocationScripts {
    // Enqueue Scripts for Admin
    public static function uc_enqueue_admin_scripts($hook) {
        $api_key = esc_attr(get_option('map_api_key'));
    
        // Check if on the Map Settings page
        if ($hook === 'location_page_map-settings') { 
            wp_enqueue_media(); // Enqueue WordPress media uploader
            wp_enqueue_script('map-settings-script', plugin_dir_url(__FILE__) . '../assets/js/map-settings.js', ['jquery'], null, true);
        }
    
        // Enqueue autocomplete script for the Location post type editor
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            global $post_type;
            if ('location' === $post_type) {
                wp_enqueue_script('google-maps-autocomplete', "https://maps.googleapis.com/maps/api/js?key=$api_key&libraries=places", [], null, true);
                wp_enqueue_script('location-admin-scripts', plugin_dir_url(__FILE__) . '../assets/js/admin-location.js', ['jquery', 'google-maps-autocomplete'], null, true);
            }
        }
        
    }
    

    // Enqueue Frontend Scripts
    public static function uc_enqueue_frontend_scripts() {
        wp_enqueue_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . esc_attr(get_option('map_api_key')) . '&libraries=places', [], null, true);
        
    }
}
