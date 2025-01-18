jQuery(document).ready(function ($) {
    var mediaUploader;

    $('#upload_pin_image_button').on('click', function (e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media({
            title: 'Choose Pin Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });
        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#map_pin_image').val(attachment.url);
            $('#pin_image_preview').attr('src', attachment.url).show();
        });
        mediaUploader.open();
    });
});
