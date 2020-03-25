/**
 * Manages JS interactions for the WP admin
 */

(function ($) {
  'use strict';

  $(document).ready(function () {
    $('#wc-discounts-media').on('click', function(e) {
      e.preventDefault();

      if (media_frame) {
        media_frame.open();
        return;
      }

      var media_frame = wp.media({
        title: wc_discounts_l10n.title,
        button: {
          text: wc_discounts_l10n.insert
        },
        library: {
          type: 'image'
        },
        multiple: false
      });

      media_frame.on('select', function () {
        // Grab url from the media selection
        var media_id = media_frame.state().get('selection').first().toJSON().id;

        $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'wc_discounts_media_upload',
            media_id: media_id,
            user_id: wc_discounts_l10n.user_id,
            _wpnonce: wc_discounts_l10n.nonce
          }
        }).done(function (data) {
          // Insert the media dynamically into the page
          // Refresh the profile pictures segement
          if (data.response) {
            $('.wc-response').html(data.response).show();
          }

          if (data.photos) {
            var photosList = $('.wc-profile-photos');

            // Start with the <ul>
            var html = '<ul class="wc-photo-list">';

            for (var key in data.photos) {
              if (data.photos.hasOwnProperty(key)) {
                html += '<li><img src="' + data.photos[key] + '" class= "wc-profile-photo"><a href="javascript:;" data-id="' + key + '" class="button button-link wc-set-default">' + wc_discounts_l10n.default + '</a></li>';
              }
            }

            // End the <ul>
            html += '</ul>';

            // Add the final HTML
            photosList.html(html);
          }
        });
      });

      media_frame.open();
    });

    $(document).on('click', '.wc-set-default', function (e) {
      e.preventDefault();

      var media_id = $(this).data('id');

      $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            action: 'wc_discounts_set_default',
            media_id: media_id,
            user_id: wc_discounts_l10n.user_id,
            _wpnonce: wc_discounts_l10n.nonce
          }
        }).done(function (data) {
          // Insert the media dynamically into the page
          // Refresh the profile pictures segement
          if (data.response) {
            $('.wc-response').html(data.response).show();
          }

          if (data.default_photo) {
            $('.wc-default-photo').html('<img src="' + data.default_photo[media_id] + '" class="wc-profile-photo">');
          }
        })
    });
  });
})(jQuery);
