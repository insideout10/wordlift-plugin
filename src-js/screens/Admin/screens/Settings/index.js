/*global wlSettings jQuery*/
/**
 * Entry: wordlift-admin-settings-page.js
 */

/**
 * Internal dependencies
 */
import './styles/index.scss';
import KeyValidator from 'modules/key-validator';
import CountryValidator from 'modules/country-validator';
import MediaUploader from 'modules/media-uploader';
import Tabs from 'modules/tabs';
import VideoAPIKeyValidator from 'modules/video-api-key-validator';

/**
 * UI interactions on the WordLift Settings page
 *
 * @since 3.11.0
 */
(($, settings) => {
  $(function() {
    // Attach the WL key validator to the `#wl-key` element.
    KeyValidator('#wl-key');
    $('#wl-key').trigger('keyup');

    // @since 3.32.7, country validation is disabled.
    //CountryValidator('#wl-country-code', '#wl-site-language');

    // Youtube API Key Validator.
    VideoAPIKeyValidator( '#wordlift_videoobject_youtube_api_key', 'youtube' );
    $('#wordlift_videoobject_youtube_api_key').trigger('keyup');

    // Vimeo API Key Validator.
    VideoAPIKeyValidator( '#wordlift_videoobject_vimeo_api_key', 'vimeo' );
    $('#wordlift_videoobject_vimeo_api_key').trigger('keyup');

    // Attach the Media Uploader to the #wl-publisher-logo
    MediaUploader(
      '#wl-publisher-media-uploader',
      {
        title: settings.l10n.logo_selection_title,
        button: settings.l10n.logo_selection_button,
        multiple: false,
        library: { type: 'image' }
      },
      attachment => {
        // Set the selected image as the preview image
        $('#wl-publisher-media-uploader-preview')
          .attr('src', attachment.url)
          .show();

        // Set the logo id.
        $('#wl-publisher-media-uploader-id').val(attachment.id);
      }
    );

    // Create the tabs.
    Tabs('.wl-tabs-element');
  });
})(jQuery, wlSettings);
