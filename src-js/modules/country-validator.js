/*global jQuery wp*/
/**
 * Validators: Country Validator.
 *
 * Validate WordLift's country.
 *
 * @since 3.18.0
 */

/**
 * Internal dependencies
 */
import delay from './delay'

// Map $ to jQuery.
const $ = jQuery

/**
 * Create a country validator that depends of the language selected.
 *
 * @since 3.18.0
 * @param {string} selector     The element selector.
 * @param {string} langSelector The selector that we wil listen to update the countries.
 */
const CountryValidator = (selector, langSelector) => {
  $(langSelector).on('change', function () {
    // Get a jQuery reference to the object.
    const $this     = $(this)
    const $selector = $(selector)

    const newOptons = [];
    const country   = $selector.val();
    const options   = $selector.data('country-codes')
    const lang      = $this.val();

    // Bail if the currently selected country allows all languages.
    // Or if the language code exists in country allowed language codes.
    // if (
    //     // If the lang attributes are empty,
    //     // then all languages are allowed.
    //     ! options[ lang ].length ||
    //     // The language code exists in country object.
    //     options[ lang ].indexOf( lang ) !== -1
    //   ) {
    //   return;
    // }

    console.log(options);

    // Post the validation request.
    wp.ajax.post(
      'wl_update_country_options',
      {lang: lang}
    )
    .done(function (data) {
      console.log(data);
      // Update the country select with new options
      $selector.html(data);
    })

  })
}

// Finally export the `CountryValidator` function.
export default CountryValidator
