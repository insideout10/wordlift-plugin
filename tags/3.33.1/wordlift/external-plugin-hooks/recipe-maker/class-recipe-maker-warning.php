<?php

namespace Wordlift\External_Plugin_Hooks\Recipe_Maker;

/**
 * Add a warning to the post edit screen if the referenced Recipe's images are less than 1.200 x 1.200
 * see issue https://github.com/insideout10/wordlift-plugin/issues/1141
 * @since 3.27.2
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Recipe_Maker_Warning {

	/**
	 * @var Recipe_Maker_Validation_Service
	 */
	private $recipe_maker_validation_service;

	public function __construct( $recipe_maker_validation_service ) {
		$this->recipe_maker_validation_service = $recipe_maker_validation_service;
		/**
		 * Filter: wl_feature__enable__notices.
		 *
		 * @param bool whether the notices needs to be enabled or not.
		 *
		 * @return bool
		 * @since 3.27.6
		 */
		if ( apply_filters( 'wl_feature__enable__notices', true ) ) {
			add_action( 'admin_notices', array( $this, 'display_image_size_warning' ) );
		}
	}

	/**
	 * Show the warning after applying the conditions.
	 */
	public function display_image_size_warning() {

		// Check if we are on the post.
		if ( ! get_post() instanceof \WP_Post ) {
			return false;
		}
		if ( ! $this->recipe_maker_validation_service->is_wp_recipe_maker_available() ) {
			return false;
		}
		$post_id = get_the_ID();

		// Dont show notification if there is no recipes referred by the post.
		if ( ! $this->recipe_maker_validation_service->is_atleast_once_recipe_present_in_the_post( $post_id ) ) {
			return false;
		}

		$recipe_with_image_warnings = $this->get_warnings( $post_id );

		if ( count( $recipe_with_image_warnings ) > 0 ) {
			// Show notification.
			?>
            <div class="notice notice-warning is-dismissible">
                <p><?php echo __( 'The following recipes didnt have minimum image size of 1200 x 1200 px', 'wordlift' ); ?></p>
                <ol>
					<?php
					foreach ( $recipe_with_image_warnings as $post_id ) {
						echo "<li>" . get_the_title( $post_id ) . "</li>";
					}
					?>
                </ol>
            </div>
			<?php
		}

	}

	/**
	 * @param $post_id
	 *
	 * @return array
	 */
	private function get_warnings( $post_id ) {

		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

		// Dont show duplicate warnings.
		$recipe_ids = array_unique( $recipe_ids );

		$recipe_with_image_warnings = array();

		foreach ( $recipe_ids as $recipe_id ) {
			$recipe     = \WPRM_Recipe_Manager::get_recipe( $recipe_id );
			$image_id   = $recipe->image_id();
			$image_data = wp_get_attachment_image_src( $image_id, array( 1200, 1200 ) );
			if ( ! is_array( $image_data ) ) {
				continue;
			}
			$image_width  = array_key_exists( 1, $image_data ) ? $image_data [1] : false;
			$image_height = array_key_exists( 2, $image_data ) ? $image_data [2] : false;
			if ( ! ( $image_height && $image_width ) ) {
				continue;
			}

			if ( $image_height < 1200 || $image_width < 1200 ) {
				// Image size not present in 1200 * 1200, show a warning.
				$recipe_with_image_warnings[] = $recipe_id;
			}
		}

		return $recipe_with_image_warnings;
	}

}
