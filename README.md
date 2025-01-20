=== Multi Location Map ===
Contributors: Uchit Chakma
Tags: maps, location, google maps, custom post type, shortcode
Requires at least: 5.0
Tested up to: 6.7.1
Requires PHP: 7.2
Stable tag: 1.1
License: GPLv2 or later
License URI: https://www.uchitchakma.com

A plugin to display a multi-location map with customizable settings, Google Maps integration, and shortcode support.

== Description ==

**Multi Location Map** is a WordPress plugin that allows you to display a Google Map with multiple locations using a shortcode. Each location is managed through a custom post type, with options for latitude, longitude, and custom pin images. You can easily customize the map's appearance and settings via the WordPress admin interface.

### Features:
- Custom post type for managing locations.
- Google Maps integration.
- Ability to add custom pin images for each location.
- Fully responsive maps with adjustable heights for desktop, tablet, and mobile.
- Easy-to-use shortcode for displaying the map on any page or post.
- Admin settings page to manage API keys, pin styles, and map dimensions.
- Lightweight and optimized for performance.

### Shortcode:
Use `[multi_location_map]` to display the map anywhere on your website.

== Installation ==

1. Download and extract the plugin zip file.
2. Upload the extracted folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Navigate to **Locations > Settings** to configure the plugin, including your Google Maps API key.

== Frequently Asked Questions ==

= How do I add locations? =
After activating the plugin, go to **Locations > Add New** in the WordPress admin dashboard. Fill in the location details, such as the title, latitude, longitude, and an optional custom pin image.

= How do I display the map on my website? =
Copy the shortcode `[multi_location_map]` and paste it into any page or post where you want the map to appear.

= How do I get a Google Maps API key? =
Follow the instructions on the [Google Maps Platform](https://developers.google.com/maps/documentation/javascript/get-api-key) to generate your API key. Add this key in the plugin settings under **Locations > Settings**.

= Does the plugin support custom CSS? =
Currently, the plugin does not have a dedicated CSS editor. However, you can style the map using your theme's CSS file or the WordPress Customizer.

= Can I use Elementor to style the map? =
Yes, the plugin supports Elementor. You can include the shortcode `[multi_location_map]` in an Elementor widget and apply custom styling.

== Screenshots ==

1. **Map with multiple locations** - Showcase your locations with custom pins on a responsive Google Map.
2. **Admin settings page** - Easily manage your API key, pin styles, and map dimensions.
3. **Custom post type for locations** - Add and edit locations from the WordPress admin interface.

== Changelog ==

= 1.1 =
* Added shortcode functionality.
* Enhanced admin settings page.
* Improved script and style handling.
* Added support for custom pin images.

= 1.0 =
* Initial release.

== Upgrade Notice ==

= 1.1 =
Upgrade to use the new shortcode functionality and improved settings page.

== License ==

This plugin is licensed under the GPLv2 or later. For more details, visit [https://www.uchitchakma.com].
