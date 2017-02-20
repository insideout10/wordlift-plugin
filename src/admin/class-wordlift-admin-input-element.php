<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 20/02/2017
 * Time: 21:51
 */
class Wordlift_Admin_Input_Element {

	public static function render( $args ) {

		// Parse the arguments and merge with default values.
		$params = wp_parse_args( $args, array(
			'id'          => uniqid( 'wl-input-' ),
			'name'        => uniqid( 'wl-input-' ),
			'value'       => '',
			'readonly'    => false,
			'class'       => false,
			'description' => false,
		) );

		// Set the readonly and class attributes.
		$readonly    = $params['readonly'] ? ' readonly="readonly"' : '';
		$css_class   = $params['class'] ? ' class="' . esc_attr( $params['class'] ) . '"' : '';
		$description = $params['description'] ? '<p>' . esc_html__( $params['description'], 'wordlift' ) . '</p>' : '';

		?>

		<input type="text"
		       id="<?php echo esc_attr( $params['id'] ); ?>"
		       name="<?php echo esc_attr( $params['name'] ); ?>"
		       value="<?php echo esc_attr( $params['value'] ); ?>"
			<?php echo $readonly; ?>
			<?php echo $css_class; ?>
		/>
		<?php echo $description; ?>

		<?php
	}

}
