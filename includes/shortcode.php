<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Renders the map using a shortcode.
 *
 * @return string The HTML output for the map.
 */
function mlm_render_map_shortcode() {
    $args = [
        'post_type'      => 'location',
        'posts_per_page' => -1,
    ];

    $locations = get_posts($args);

    $map_data = [];
    $default_pin_image = esc_url(get_option('map_pin_image'));
    $pin_size = intval(get_option('map_pin_size', 40)); // Default size to 40px if not set
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
            'pin_image' => $custom_pin_image ? $custom_pin_image : $default_pin_image,
        ];
    }

    ob_start();
    ?>
    <style>
        #map {
            width: 100%;
        }
        @media (max-width: 768px) {
            #map {
                height: <?php echo $map_height_mobile; ?>px;
            }
        }
        @media (min-width: 769px) and (max-width: 1024px) {
            #map {
                height: <?php echo $map_height_tablet; ?>px;
            }
        }
        @media (min-width: 1025px) {
            #map {
                height: <?php echo $map_height_desktop; ?>px;
            }
        }
    </style>
    <div id="map"></div>
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 5,
                center: { lat: 20.5937, lng: 78.9629 }, // Default to India's center
            });
            const bounds = new google.maps.LatLngBounds();
            const locations = <?php echo json_encode($map_data); ?>;
            let activeInfoWindow = null;

            locations.forEach(location => {
                const position = { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) };
                const pinImage = {
                    url: location.pin_image,
                    scaledSize: new google.maps.Size(<?php echo $pin_size; ?>, <?php echo $pin_size; ?>)
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
                        activeInfoWindow.close();
                    }
                    infowindow.open(map, marker);
                    activeInfoWindow = infowindow;
                });
            });

            map.fitBounds(bounds);
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?php echo esc_attr(get_option('map_api_key')); ?>&callback=initMap" async defer></script>
    <?php
    return ob_get_clean();
}
add_shortcode('multi_location_map', 'mlm_render_map_shortcode');
