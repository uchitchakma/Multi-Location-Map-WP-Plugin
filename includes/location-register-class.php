<?php
namespace MultiLocationMap;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class UC_RegisterLocation {
        // Register Custom Post Type
        public static function register_post_type() {
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
    }