<?php
/**
 * Ingredient Meta Box.
 *
 * @author Mahbub Hasan Imon <mahbub@wordlift.io>
 * @package Wordlift
 */

namespace Wordlift\Modules\Food_Kg\Admin;

use Wordlift\Api\Api_Service_Ext;
use Wordlift\Modules\Food_Kg\Recipe_Lift_Strategy;

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
		add_action( 'wp_ajax_wl_ingredient_autocomplete', array( $this, 'wl_ingredient_autocomplete' ) );
	}

	/**
	 * Ingredients HTML.
	 *
	 * @param array $recipe_ids Recipe IDs.
	 */
	public function ingredients_html( $recipe_ids ) {

		// Enqueue scripts.
		$this->enqueue_scripts();

		if ( empty( $recipe_ids ) ) {
			return;
		}
		?>
		<div class="wl-recipe-ingredient">
			<p>
				<?php
				$count = count( $recipe_ids );
				/* translators: 1: Number of recipes 2: Review notice */
				echo sprintf(
					'%1$s %2$s',
					/* translators: %d: Number of recipes */
					esc_html( sprintf( 1 < $count ? __( 'There are %d recipes embedded in this post.', 'wordlift' ) : __( 'There is %d recipe embedded in this post.', 'wordlift' ), $count ) ),
					esc_html__( 'Review the main ingredient for each recipe and change it if required.', 'wordlift' )
				);
				?>
			</p>
		</div>
		<div class="wl-recipe-ingredient-form" id="wl-recipe-ingredient-form">
			<table class="wl-table wl-table--main-ingredient">
				<thead>
				<tr>
					<th class="wl-table__th wl-table__th--recipe"><?php esc_html_e( 'Recipe', 'wordlift' ); ?></th>
					<th class="wl-table__th wl-table__th--main-ingredient"><?php esc_html_e( 'Main Ingredient', 'wordlift' ); ?></th>
					<th><?php esc_html_e( 'Action', 'wordlift' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $recipe_ids as $recipe_id ) {
					$recipe          = \WPRM_Recipe_Manager::get_recipe( $recipe_id );
					$json_ld         = get_post_meta( $recipe_id, '_wl_main_ingredient_jsonld', true );
					$obj             = json_decode( $json_ld );
					$main_ingredient = isset( $obj->name ) ? $obj->name : '<em>' . __( '(unset)', 'wordlift' ) . '</em>';
					?>
					<tr class="wl-table--main-ingredient__data">
						<td><?php echo esc_html( $recipe->name() ); ?></td>
						<td><?php echo wp_kses( $main_ingredient, array( 'em' => array() ) ); ?></td>
						<td class="wl-table__ingredients-data">
							<input type="hidden" id="recipe-id" name="recipe-id" value="<?php echo esc_attr( $recipe_id ); ?>">
							<span class="wl-select-main-ingredient"></span>
						</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>

			<input type="submit" class="button button-primary button-large pull-right wl-recipe-ingredient-form__submit"
					value="<?php echo esc_attr__( 'Save', 'wordlift' ); ?>">
		</div>
		<?php
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

		// Clear any buffer.
		ob_clean();

		if ( $data ) {
			$results = array(
				array(
					'label' => $data->name,
					'value' => $new_json_ld,
				),
				array(
					'label' => __( 'Don’t change', 'wordlift' ),
					'value' => 'dont-change',
				),
				array(
					'label' => __( 'Unset', 'wordlift' ),
					'value' => 'unset',
				),
			);
			wp_send_json_success( $results );
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'No results found.', 'wordlift' ),
				)
			);
		}
	}

	/**
	 * Update Ingredient Post Meta.
	 */
	public function update_ingredient_post_meta() {
		check_ajax_referer( 'wl-ingredient-nonce' );

		// Check if current user can edit posts.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You are not allowed to edit posts.', 'wordlift' ),
					'btnText' => __( 'Denied', 'wordlift' ),
				)
			);
		}

		// Return error if the recipe id is empty.
		if ( ! empty( $_REQUEST['recipe_id'] ) ) { // Input var okay.
			$recipe_id = sanitize_text_field( wp_unslash( $_REQUEST['recipe_id'] ) ); // Input var okay.
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The recipe id is empty.', 'wordlift' ),
					'btnText' => __( 'Failed', 'wordlift' ),
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
					'btnText' => __( 'Failed', 'wordlift' ),
				)
			);
		}

		if ( 'unset' === $main_ingredient ) {
			$update = delete_post_meta( $recipe_id, '_wl_main_ingredient_jsonld' );
		} elseif ( 'dont-change' === $main_ingredient ) {
			// $old_json_ld = get_post_meta( $recipe_id, '_wl_main_ingredient_jsonld', true );
			$update = true;
			echo 'Don\'t change';
		} else {
			$update = update_post_meta( $recipe_id, '_wl_main_ingredient_jsonld', $main_ingredient );
			if ( ! $update ) {
				wp_send_json_error(
					array(
						'message' => __( 'The main ingredient could not be updated.', 'wordlift' ),
						'btnText' => __( 'Failed', 'wordlift' ),
					)
				);
			}
		}

		if ( $update ) {
			wp_send_json_success(
				array(
					'message' => __( 'The main ingredient has been updated.', 'wordlift' ),
					'btnText' => __( 'Saved', 'wordlift' ),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The main ingredient could not be updated.', 'wordlift' ),
					'btnText' => __( 'Failed', 'wordlift' ),
				)
			);
		}
	}

	/**
	 * Enqueue Scripts.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'wl-main-ingredient-select', WL_DIR_URL . 'js/dist/main-ingredient-select.js', array(), WORDLIFT_VERSION, true );
		wp_enqueue_script(
			'wl-meta-box-ingredient',
			WL_DIR_URL . 'js/dist/ingredients-meta-box.js',
			array(
				'jquery',
				'jquery-ui-autocomplete',
			),
			WORDLIFT_VERSION,
			true
		);

		wp_localize_script(
			'wl-meta-box-ingredient',
			'_wlRecipeIngredient',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wl-ingredient-nonce' ),
				'acNonce' => wp_create_nonce( 'wl-ac-ingredient-nonce' ),
				'texts'   => array(
					'saving'    => __( 'Saving...', 'wordlift' ),
					'noResults' => __( 'No results found.', 'wordlift' ),
				),
			)
		);
	}
}
