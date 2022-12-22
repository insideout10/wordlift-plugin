<?php

namespace Wordlift\External_Plugin_Hooks\Recipe_Maker;

/**
 * Add a warning to the post edit screen if the referenced Recipe's images are less than 1.200 x 1.200
 * see issue https://github.com/insideout10/wordlift-plugin/issues/1141
 *
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
		add_action(
			'load-post.php',
			function () {
				add_action( 'wordlift_admin_notices', array( $this, 'display_image_size_warning' ) );
			}
		);

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

		$warnings = $this->get_warnings( $post_id );

		if ( count( $warnings ) > 0 ) {
			// Show notification.
			?>
			<div class="notice notice-warning is-dismissible">
				<h3><?php esc_html_e( 'WordLift', 'wordlift' ); ?></h3>
				<p><?php esc_html_e( 'The following recipes don\'t have minimum image size of 1200 x 1200 px. This size is required for channels like Google Discover', 'wordlift' ); ?></p>
				<ol>
					<?php
					foreach ( $warnings as $warning ) {
						$image_link = get_edit_post_link( $warning['image_id'] );
						?>
						<li><?php echo esc_html( get_the_title( $warning['recipe_id'] ) ); ?> <a href="<?php echo esc_attr( $image_link ); ?>"><?php esc_html_e( '[edit image]', 'wordlift' ); ?></a></li>
						<?php
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
			$recipe = \WPRM_Recipe_Manager::get_recipe( $recipe_id );
			if ( ! $recipe ) {
				continue;
			}
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
				$recipe_with_image_warnings[] = array(
					'recipe_id' => $recipe_id,
					'image_id'  => $image_id,
				);
			}
		}

		return $recipe_with_image_warnings;
	}

}
