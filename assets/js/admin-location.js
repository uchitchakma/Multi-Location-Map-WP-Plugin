jQuery(document).ready(function ($) {
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
