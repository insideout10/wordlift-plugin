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
			<?php echo $this->get_data_attributes( $params['data'] ); ?>
		>
			<?php $this->render_options( $params ); ?>
		</select>

		<?php if ( ! empty( $params['notice'] ) ) : ?>
			<small class="wl-select-notices">
				<?php echo $params['notice']; ?>
			</small>
		<?php
		endif;

		// Print the field description.
		echo $this->get_description( $params['description'] );

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
	 * Adds data attributes to select element.
	 *
	 * We need to use method here, because different select elements
	 * may have different data attributes.
	 *
	 * @param array $data Array of all data attributes.
	 *
	 * @since 3.18.0
	 *
	 * @return string $output The data attributes or empty string
	 */
	private function get_data_attributes( $data ) {
		// The output.
		$output = '';

		// Bail is there are no data attributes.
		if ( empty( $data ) ) {
			return $output;
		}

		// Loop throught all data attributes and build the output string.
		foreach ( $data as $name => $value ) {
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
	protected function enqueue_resources(){}

	/**
	 * Abstract method that renders the select options.
	 *
	 * @since 3.18.0
	 *
	 * @param array $params Select params.
	 *
	 * @return Prints the select options.
	 */
	abstract function render_options( $params );
}
