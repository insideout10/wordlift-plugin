/*global jQuery wp*/
/**
 * Validators: Video API Key Validator.
 *
 * Validate WordLift's Video Settings API key in inputs.
 *
 * @since 3.40.1
 */

/**
 * Internal dependencies
 */
import delay from './delay'

// Map $ to jQuery.
const $ = jQuery

/**
 * Create a Video API key validator on the element with the specified selector.
 *
 * @since 3.11.0
 * @param {string} selector The element selector.
 * @param {string} type Type.
 */
const VideoAPIKeyValidator = (selector, type) => {
  $(selector).on('keyup', function () {
    // Get a jQuery reference to the object.
    const $this = $(this)

    const settings = window["wlSettings"] || {};

    // Remove any preexisting states, including the `untouched` class
    // which is set initially to prevent displaying the
    // `valid`/`invalid` indicator.
    $this.removeClass('untouched valid invalid')

    // Delay execution of the validation.
    delay($this, function () {
      // Post the validation request.
      wp.ajax.post(
        'wl_validate_video_api_key',
        {
          api_key: $this.val(),
          type: type,
          _wpnonce: settings['wl_video_api_nonce']
        })
        .done(function (data) {
            $this.addClass('valid')
        })
        .fail(function (data) {
            $this.addClass('invalid')
        } )
    })
  })
}

export default VideoAPIKeyValidator
