<?php
/**
 * Ingredient Meta Box.
 *
 * @author Mahbub Hasan Imon <mahbub@wordlift.io>
 * @package Wordlift
 */
namespace Wordlift\Modules\Food_Kg;

use Wordlift\Api\Api_Service_Ext;

class Meta_Box {

	/**
	 * @var Api_Service_Ext
	 */
	private $api_service;

	/**
	 * @var Recipe_Lift_Strategy
	 */
	private $recipe_lift_strategy;

	/**
	 * @param Api_Service_Ext      $api_service
	 * @param Recipe_Lift_Strategy $recipe_lift_strategy
	 */
	public function __construct( Api_Service_Ext $api_service, Recipe_Lift_Strategy $recipe_lift_strategy ) {
		$this->api_service          = $api_service;
		$this->recipe_lift_strategy = $recipe_lift_strategy;
	}

	/**
	 * Register Hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
		add_action( 'wl_ingredient_metabox_html', array( $this, 'ingredients_html' ) );
		add_action( 'wp_ajax_wl_update_ingredient_post_meta', array( $this, 'update_ingredient_post_meta' ) );
		add_action( 'wp_ajax_nopriv_wl_update_ingredient_post_meta', array( $this, 'update_ingredient_post_meta' ) );
		add_action( 'wp_ajax_wl_ingredient_autocomplete', array( $this, 'wl_ingredient_autocomplete' ) );
		add_action( 'wp_ajax_nopriv_wl_ingredient_autocomplete', array( $this, 'wl_ingredient_autocomplete' ) );
	}

	/**
	 * Ingredients HTML.
	 */
	public function ingredients_html() {

		// Enqueue scripts.
		$this->enqueue_scripts();

		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( get_the_ID() );
		if ( ! empty( $recipe_ids ) ) {
			foreach ( $recipe_ids as $key => $recipe_id ) {
				$recipe_json_ld = get_post_meta( $recipe_id, '_wl_main_ingredient_jsonld', true );
				if ( $recipe_json_ld ) {
					$recipe = json_decode( $recipe_json_ld, true );
					if ( isset( $recipe['name'] ) ) {
						?>
						<div class="wl-recipe-ingredient">
							<?php echo sprintf( '<p>The main ingredient is <strong>%s</strong></p>', esc_html( $recipe['name'] ) ); ?>
							<form class="wl-recipe-ingredient-form" id="wl-recipe-ingredient-form-<?php echo esc_attr( $key ); ?>">
								<label for="wl-recipe-ingredient__field-<?php echo esc_attr( $recipe['name'] ) . '-' . esc_attr( $key ); ?>"><?php echo esc_html__( 'Replace the main ingredient', 'wordlift' ); ?>: </label>
								<input class="main-ingredient" id="wl-recipe-ingredient__field-<?php echo esc_attr( $recipe['name'] ) . '-' . esc_attr( $key ); ?>" name="main_ingredient" >
								<input type="hidden" id="recipe_id" name="recipe_id" value="<?php echo esc_attr( $recipe_id ); ?>">
								<button type="submit" class="wl-recipe-ingredient__save"><?php echo esc_html__( 'Save', 'wordlift' ); ?></button>
							</form>
						</div>
						<?php
					}
				}
			}
		}
	}

	/**
	 * Ingredient Autocomplete.
	 */
	public function wl_ingredient_autocomplete() {

		check_ajax_referer( 'wl-ac-ingredient-nonce' );

		// Return error if the query param is empty.
		if ( ! empty( $_REQUEST['query'] ) ) { // Input var okay.
			$query = sanitize_text_field( wp_unslash( $_REQUEST['query'] ) ); // Input var okay.
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The query param is empty.', 'wordlift' ),
				)
			);
		}

		// Get new JSON LD Data.
		$new_json_ld = $this->recipe_lift_strategy->get_json_ld_data( $query );

		$data = json_decode( $new_json_ld );

		$results = array( $data->name );

		// Clear any buffer.
		ob_clean();

		wp_send_json_success( $results );
	}

	/**
	 * Update Ingredient Post Meta.
	 */
	public function update_ingredient_post_meta() {
		check_ajax_referer( 'wl-ingredient-nonce' );

		// Return error if the recipe id is empty.
		if ( ! empty( $_REQUEST['recipe_id'] ) ) { // Input var okay.
			$recipe_id = sanitize_text_field( wp_unslash( $_REQUEST['recipe_id'] ) ); // Input var okay.
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The recipe id is empty.', 'wordlift' ),
				)
			);
		}

		// Return error if the main ingredient is empty.
		if ( ! empty( $_REQUEST['main_ingredient'] ) ) {
			$main_ingredient = sanitize_text_field( wp_unslash( $_REQUEST['main_ingredient'] ) );
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The main ingredient is empty.', 'wordlift' ),
				)
			);
		}

		// Get new JSON LD Data.
		$new_json_ld = $this->recipe_lift_strategy->get_json_ld_data( $main_ingredient );

		if ( $new_json_ld ) {
			// Update the recipe post meta for JSON-LD.
			$update = update_post_meta( $recipe_id, '_wl_main_ingredient_jsonld', $new_json_ld );
			if ( $update ) {
				wp_send_json_success(
					array(
						'message' => __( 'The main ingredient has been updated.', 'wordlift' ),
					)
				);
			} else {
				wp_send_json(
					array(
						'same'    => true,
						'message' => __( 'You didn\'t updated the main ingredient value.', 'wordlift' ),
					)
				);
			}
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'Failed to Update Recipe Ingredient.', 'wordlift' ),
				)
			);
		}
	}

	/**
	 * Enqueue Scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'wl-meta-box-ingredient', WL_DIR_URL . 'js/dist/ingredients-meta-box.js', array( 'jquery', 'jquery-ui-autocomplete' ), WORDLIFT_VERSION, true );

		wp_localize_script(
			'wl-meta-box-ingredient',
			'wlRecipeIngredient',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wl-ingredient-nonce' ),
				'acNonce' => wp_create_nonce( 'wl-ac-ingredient-nonce' ),
			)
		);
	}
}
