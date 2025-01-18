<?php

function mldm_map_shortcode() {
    return '<div id="myMap" style="height: 700px; width: 100%;"></div>';
}
add_shortcode('multi_location_dynamic_map', 'mldm_map_shortcode');
