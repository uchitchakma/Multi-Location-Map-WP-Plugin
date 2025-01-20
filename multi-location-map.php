<?php
/**
 * Plugin Name: Multi Location Map
 * Description: A plugin to display a multi-location map with popups and customizable settings, powered by Google Maps.
 * Version: 1.1
 * Requires at least: 5.0
 * Tested up to: 6.7.1
 * Requires PHP: 7.2
 * Author: Uchit Chakma
 * Author URI: https://uchitchakma.com
 * License: GPLv2 or later
 * Text Domain: multi-location-map
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants.
define('MLM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MLM_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once MLM_PLUGIN_DIR . 'includes/location-register-class.php'; // Custom post type registration.
require_once MLM_PLUGIN_DIR . 'includes/location-meta-box-class.php';
require_once MLM_PLUGIN_DIR . 'includes/location-settings-class.php';
require_once MLM_PLUGIN_DIR . 'includes/location-shortcode-class.php'; // Shortcode rendering.
require_once MLM_PLUGIN_DIR . 'includes/location-scripts-class.php';

use MultiLocationMap\UC_RegisterLocation;
use MultiLocationMap\UC_LocationMetaBox;
use MultiLocationMap\UC_LocationSettings;
use MultiLocationMap\UC_LocationScripts;
use MultiLocationMap\UC_LocationShortcode;


class MultiLocationMap {

    public function __construct() {
        // Hooks
        add_action('init', [UC_RegisterLocation::class, 'register_post_type']); // Custom post type registration
        add_action('add_meta_boxes', [UC_LocationMetaBox::class, 'register_meta_boxes']); // Register meta boxes
        add_action('save_post', [UC_LocationMetaBox::class, 'save_meta_boxes']); // Save meta box data
        add_action('admin_menu', [UC_LocationSettings::class, 'register_settings_menu']); // Register settings menu
        add_action('admin_init', [UC_LocationSettings::class, 'register_settings']); // Register settings
        add_action('admin_enqueue_scripts', [UC_LocationScripts::class, 'uc_enqueue_admin_scripts']); // Enqueue admin scripts
        add_action('wp_enqueue_scripts', [UC_LocationScripts::class, 'uc_enqueue_frontend_scripts']); // Enqueue frontend scripts
        add_shortcode('multi_location_map', [UC_LocationShortcode::class, 'render_map_shortcode']); // Shortcode rendering
        // Modify footer text only for this plugin's settings page
        add_action('admin_head', [$this, 'hide_wpfooter']);

    }
    public  function hide_wpfooter() {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'location_page_map-settings') {
            echo '<style>#wpfooter { display: none !important; }</style>';
        }
    }
    
    
    

}

new MultiLocationMap();