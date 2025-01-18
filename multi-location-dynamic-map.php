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

// Define constants for plugin paths
define('MLM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MLM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once MLM_PLUGIN_DIR . 'includes/custom-post-type.php';
require_once MLM_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once MLM_PLUGIN_DIR . 'includes/settings.php';
require_once MLM_PLUGIN_DIR . 'includes/shortcode.php';
require_once MLM_PLUGIN_DIR . 'includes/enqueue-scripts.php';

// Initialize the plugin
class MultiLocationMap {
    public function __construct() {
        add_action('init', 'mlm_register_custom_post_type');
        add_action('add_meta_boxes', 'mlm_register_location_meta_boxes');
        add_action('save_post', 'mlm_save_location_meta_boxes');
        add_action('admin_menu', 'mlm_register_settings_menu');
        add_action('admin_init', 'mlm_register_settings');
        add_action('wp_enqueue_scripts', 'mlm_enqueue_frontend_scripts');
        add_action('admin_enqueue_scripts', 'mlm_admin_enqueue_scripts');
        add_shortcode('multi_location_map', 'mlm_render_map_shortcode');
    }
}

// Instantiate the plugin
new MultiLocationMap();
