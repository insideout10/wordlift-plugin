/*global jQuery wp*/
/**
 * Validators: Country Validator.
 *
 * Validate WordLift's country.
 *
 * @since 3.18.0
 */

// Map $ to jQuery.
const $ = jQuery

/**
 * Create a country validator that depends of the language selected.
 *
 * @since 3.18.0
 * @param {string} selector     The element selector.
 * @param {string} langSelector The selector that we wil listen to update the countries.
 */
const CountryValidator = (countrySelector, langSelector) => {
  $(countrySelector + ', ' + langSelector).on('change', function () {
    // Get jQuery references to the required object.
    const $countrySelect  = $(countrySelector)
    const $notices        = $countrySelect.siblings('.wl-select-notices')

    // Get values.
    const selectedCountry = $countrySelect.val();
    const options         = $countrySelect.data('country-codes')
    const selectedLang    = $(langSelector).val();

    // Notify the user that the selected country doens't support the chosen language.
    if (
        // Check that the country code exists in predefined country codes.
        typeof( options[ selectedCountry ] ) !== 'undefined' &&
        // And there are predefined languages for chosen country.
        options[ selectedCountry ].length &&
        // And chosen language doesn't exists
        options[ selectedCountry ].indexOf( selectedLang ) === -1
    ) {
      // Add notice.
      $notices.html( 'The selected language is not supported in this country.</br>Please choose another country or langugage.' );
    } else {
      // Remove the notice.
      $notices.html( '' );
    }

    // Post the validation request.
    wp.ajax.post(
      'wl_update_country_options',
      {
        lang: selectedLang,
        value: selectedCountry
      }
    )
    .done(function (data) {
      // Update the country select with new options
      $countrySelect.html(data);
    })
  })
}

// Finally export the `CountryValidator` function.
export default CountryValidator
