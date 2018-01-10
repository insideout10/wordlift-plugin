<?php
/**
 * Elements: Select.
 *
 * An Select element with the list of options.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Select_Element} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
abstract class Wordlift_Admin_Select_Element implements Wordlift_Admin_Element {

	/**
	 * @inheritdoc
	 */
	public function render( $args ) {

		// Parse the arguments and merge with default values.
		$params = wp_parse_args(
			$args,
			array(
				'id'          => uniqid( 'wl-input-' ),
				'name'        => uniqid( 'wl-input-' ),
				'value'       => '',
				'description' => false,
			)
		);

		$description = $params['description'] ? '<p>' . wp_kses( $params['description'], array( 'a' => array( 'href' => array() ) ) ) . '</p>' : '';

		?>
		<select
			id="<?php echo esc_attr( $params['id'] ); ?>"
		    name="<?php echo esc_attr( $params['name'] ); ?>"
		>
			<?php $this->render_options( $params['value'] ); ?>
		</select>
		<?php
		// Print the field description.
		echo $description;

		return $this;
	}

	/**
	 * Abstract method that renders the select options.
	 * 
	 * @since 3.18.0
	 * 
	 * @param string $current_value Select selected option.
	 * 
	 * @return Prints the select options.
	 */
	abstract function render_options( $current_value );
}
