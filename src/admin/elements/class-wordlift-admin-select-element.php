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
		// Some select fields may need custom script/styles to work.
		$this->enqueue_resources();

		// Parse the arguments and merge with default values.
		$params = wp_parse_args(
			$args,
			array(
				'id'          => uniqid( 'wl-input-' ),
				'name'        => uniqid( 'wl-input-' ),
				'value'       => '',
				'class'       => '',
				'notice'      => '',
				'description' => false,
				'data'        => array(),
				'options'     => array(),
			)
		);
		?>
		<select
				id="<?php echo esc_attr( $params['id'] ); ?>"
				name="<?php echo esc_attr( $params['name'] ); ?>"
				class="<?php echo esc_attr( $params['class'] ); ?>"
			<?php echo $this->get_data_attributes( $params['data'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		>
			<?php $this->render_options( $params ); ?>
		</select>

		<?php
		// Print the notice message if there is such.
		$this->print_notice( $params['notice'] );

		// Print the field description.
		echo wp_kses( $this->get_description( $params['description'] ), array( 'p' => array() ) );

		return $this;
	}

	/**
	 * Returns html escaped description string.
	 * Note: Only `a` tags are allowed with only `href` attributes.
	 *
	 * @param string|bool $description The field description or false if not set.
	 *
	 * @since 3.18.0
	 *
	 * @return string|void The description or null if not set.
	 */
	public function get_description( $description ) {
		// Bail if the description is not set.
		if ( empty( $description ) ) {
			return;
		}

		// Remove all characters except links.
		$filtered_descrption = wp_kses(
			$description,
			array(
				'a' => array(
					'href' => array(),
				),
			)
		);

		return wpautop( $filtered_descrption );
	}

	/**
	 * Prints the field notice that will be shown on error.
	 *
	 * @param string $notice The notice to add.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	public function print_notice( $notice ) {
		// Bail if the notice is empty.
		if ( empty( $notice ) ) {
			return;
		}

		// Print the notice message.
		printf(
			'<small class="wl-select-notices">%s</small>',
			esc_html( $notice )
		);
	}

	/**
	 * Adds data attributes to select element.
	 *
	 * We need to use method here, because different select elements
	 * may have different data attributes.
	 *
	 * @param array $pre_attributes Array of all data attributes.
	 *
	 * @since 3.18.0
	 *
	 * @return string $output The data attributes or empty string
	 */
	private function get_data_attributes( $pre_attributes ) {
		// The output.
		$output = '';

		/**
		 * Filter: 'wl_admin_select_element_data_attributes' - Allow third
		 * parties to modify the field data attrbutes.
		 *
		 * @since  3.18.0
		 *
		 * @param  array $pre_attributes Array of the field data attributes.
		 */
		$attributes = apply_filters( 'wl_admin_select_element_data_attributes', $pre_attributes );

		// Bail is there are no data attributes.
		if ( empty( $attributes ) ) {
			return $output;
		}

		// Loop throught all data attributes and build the output string.
		foreach ( $attributes as $name => $value ) {
			$output .= sprintf(
				'data-%s="%s" ',
				$name,
				esc_attr( $value )
			);
		}

		// Finally return the output escaped.
		return $output;
	}

	/**
	 * Prints specific resources(scripts/styles) if the select requires them.
	 *
	 * @since 3.18.0
	 *
	 * @return void
	 */
	protected function enqueue_resources() {

	}

	/**
	 * Abstract method that renders the select options.
	 *
	 * @since 3.18.0
	 *
	 * @param array $params Select params.
	 *
	 * @return Prints the select options.
	 */
	abstract public function render_options( $params );

}
