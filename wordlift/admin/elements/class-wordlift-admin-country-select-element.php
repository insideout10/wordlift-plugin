<?php
/**
 * Elements: Country Select.
 *
 * An Select element with the list of countries.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Country_Select_Element} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Country_Select_Element extends Wordlift_Admin_Select_Element {

	/**
	 * Adds a filter that will add data `country-codes` attrbiute to the country select
	 * to allow front-end validation.
	 *
	 * @since 3.18.0
	 */
	public function __construct() {
		// Adds the country codes as data attribute to allow front-end validation.
		add_filter( 'wl_admin_select_element_data_attributes', array( $this, 'add_country_codes_data' ), 10, 1 );
	}

	/**
	 * @inheritdoc
	 */
	public function render_options( $params ) {
		// Print all the supported countries, preselecting the one configured
		// in WP (or United Kingdom if not supported). We now use the `Wordlift_Countries`
		// class which provides the list of countries supported by WordLift.
		//
		// https://github.com/insideout10/wordlift-plugin/issues/713

		$lang = ( isset( $params['lang'] ) ) ? $params['lang'] : false;

		// Get WordLift's supported countries.
		$countries = Wordlift_Countries::get_countries( $lang );

		// If we support WP's configured language, then use that, otherwise use English by default.
		$language = isset( $countries[ $params['value'] ] ) ? $params['value'] : 'uk';

		foreach ( $countries as $code => $label ) :
			?>
			<option
					value="<?php echo esc_attr( $code ); ?>"
				<?php echo selected( $code, $language, false ); ?>
			>
				<?php echo esc_html( $label ); ?>
			</option>
			<?php
		endforeach;
	}

	/**
	 * Returns select options html.
	 *
	 * @since 3.18.0
	 *
	 * @return void Echoes select options or empty string if required params are not set.
	 */
	public function get_options_html() {
		$html = '';

		// Check whether the required params are set.
        // phpcs:ignore Standard.Category.SniffName.ErrorCode
		if ( ! empty( $_POST['lang'] ) && ! empty( $_POST['value'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			ob_start();
			// Get the new options.
			// phpcs:ignore Standard.Category.SniffName.ErrorCode
			$this->render_options( $_POST ); //phpcs:ignore WordPress.Security.NonceVerification.Missing

			$html = ob_get_clean();
		}

		// Return the html.
		wp_send_json_success( $html );
	}

	/**
	 * Modify the field data attributes by adding`country-code`
	 * to existing attributes.
	 *
	 * @param array $attributes Current data attributes.
	 *
	 * @since 3.18.0
	 *
	 * @return array $attributes Modified attributes.
	 */
	public function add_country_codes_data( $attributes ) {
		// Add the country codes.
		$attributes['country-codes'] = wp_json_encode( Wordlift_Countries::get_codes() );

		// Return the attributes.
		return $attributes;
	}

}
