jQuery(document).ready(function ($) {
    // Initialize the Google Places Autocomplete
    if (typeof google === 'object' && typeof google.maps === 'object') {
        const input = document.getElementById('location_name');
        const autocomplete = new google.maps.places.Autocomplete(input);

        // Restrict autocomplete to specific country or region (optional)
        autocomplete.setComponentRestrictions({});

        // When a place is selected, populate latitude and longitude
        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                $('#latitude').val(place.geometry.location.lat());
                $('#longitude').val(place.geometry.location.lng());
            }
        });
    } else {
        console.error('Google Maps JavaScript API is not loaded.');
    }

    // Custom Pin Image Upload
    let mediaUploader;
    $('#upload_custom_pin_image_button').click(function (e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Custom Pin Image',
            button: {
                text: 'Use this image',
            },
            multiple: false,
        });
        mediaUploader.on('select', function () {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#custom_pin_image').val(attachment.url);
            $('#custom_pin_image_preview').attr('src', attachment.url).show();
        });
        mediaUploader.open();
    });
});
