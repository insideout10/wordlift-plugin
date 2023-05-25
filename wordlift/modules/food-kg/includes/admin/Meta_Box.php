<?php
/**
 * Ingredient Meta Box.
 *
 * @author Mahbub Hasan Imon <mahbub@wordlift.io>
 * @package Wordlift
 */

namespace Wordlift\Modules\Food_Kg\Admin;

use Wordlift\Api\Api_Service_Ext;
use Wordlift\Cache\Ttl_Cache;
use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
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
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_action( 'wl_ingredient_metabox_html', array( $this, 'ingredients_html' ) );
		add_action(
			'wp_ajax_wl_update_ingredient_post_meta',
			array(
				$this,
				'update_ingredient_post_meta',
			)
		);
		add_action(
			'wp_ajax_wl_ingredient_autocomplete',
			array(
				$this,
				'wl_ingredient_autocomplete',
			)
		);
		add_action( 'wl_metabox_html', array( $this, 'metabox_tab' ) );
	}

	private function has_recipes( $post_id ) {

		$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( $post_id );

		return 0 < count( $recipe_ids );
	}

	public function metabox_tab() {
		// Only display the Main Ingredient tab if the feature and WP Recipe Maker plugin enabled.
		if ( get_the_ID() && $this->has_recipes( get_the_ID() ) ) { // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			$recipe_ids = \WPRM_Recipe_Manager::get_recipe_ids_from_post( get_the_ID() );
			?>
		  <input id="wl-tab-main-ingredient" type="radio" name="wl-metabox-tabs"/><label
			  for="wl-tab-main-ingredient"><?php esc_html_e( 'Main Ingredient', 'wordlift' ); ?></label>
		  <div class="wl-tabs__tab"><?php $this->ingredients_html( $recipe_ids ); ?></div>
			<?php
		}
	}

	public function enqueue_block_editor_assets() {
		// Requires settings defined in $this->enqueue_scripts.
		wp_enqueue_script( 'wl-main-ingredient-select', WL_DIR_URL . 'js/dist/main-ingredient-select.js', array(), WORDLIFT_VERSION, true );
	}

	/**
	 * Ingredients HTML.
	 *
	 * @param array $recipe_ids Recipe IDs.
	 */
	public function ingredients_html( $recipe_ids ) {

		if ( empty( $recipe_ids ) ) {
			return;
		}

		// Enqueue scripts.
		$this->enqueue_scripts();

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
			$content_service = Wordpress_Content_Service::get_instance();
			foreach ( $recipe_ids as $recipe_id ) {
				$content_id      = Wordpress_Content_Id::create_post( $recipe_id );
				$recipe          = \WPRM_Recipe_Manager::get_recipe( $recipe_id );
				$json_ld         = $content_service->get_about_jsonld( $content_id );
				$obj             = json_decode( $json_ld );
				$main_ingredient = isset( $obj->name ) ? $obj->name : '<em>' . __( '(unset)', 'wordlift' ) . '</em>';
				?>
			<tr class="wl-table--main-ingredient__data">
			  <td><?php echo esc_html( $recipe->name() ); ?></td>
			  <td><?php echo wp_kses( $main_ingredient, array( 'em' => array() ) ); ?></td>
			  <td class="wl-table__ingredients-data">
							<span class="wl-select-main-ingredient"
					data-recipe-id="<?php echo esc_attr( $recipe_id ); ?>"></span>
			  </td>
			</tr>
				<?php
			}
			?>
		  </tbody>
		</table>
		<div class="wl-recipe-ingredient-form__submit">
		  <div id="wl-recipe-ingredient-form__submit__message"></div>
		  <input type="submit"
				 class="button button-primary button-large pull-right"
				 id="wl-recipe-ingredient-form__submit__btn"
				 value="<?php echo esc_attr__( 'Save', 'wordlift' ); ?>">
		</div>
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
		// Return error if the recipe data is empty.
		if ( ! empty( $_REQUEST['data'] ) ) {
			$recipe_data = sanitize_text_field( wp_unslash( $_REQUEST['data'] ) );
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'Recipe data is empty.', 'wordlift' ),
					'btnText' => __( 'Failed', 'wordlift' ),
				)
			);
		}

		$recipes = json_decode( $recipe_data );

		$updated         = false;
		$content_service = Wordpress_Content_Service::get_instance();
		foreach ( $recipes as $recipe ) {
			$recipe_id       = $recipe->recipe_id;
			$content_id      = Wordpress_Content_Id::create_post( $recipe_id );
			$main_ingredient = $recipe->ingredient;
			if ( 'UNSET' === $main_ingredient ) {
				$updated = $content_service->set_about_jsonld( $content_id, null );
			} elseif ( 'DONT_CHANGE' === $main_ingredient ) {
				$updated = true;
			} else {
				$main_ingredient = wp_json_encode( json_decode( $recipe->ingredient, true ) );
				$updated         = $content_service->set_about_jsonld( $content_id, $main_ingredient );
			}
		}

		// Since we changed the main ingredients we want to flush all caches.
		Ttl_Cache::flush_all();

		if ( $updated ) {
			wp_send_json_success(
				array(
					'message' => __( 'The main ingredient has been updated.', 'wordlift' ),
				)
			);
		} else {
			wp_send_json_error(
				array(
					'message' => __( 'The main ingredient could not be updated.', 'wordlift' ),
				)
			);
		}
	}

	/**
	 * Enqueue Scripts.
	 */
	public function enqueue_scripts() {

		wp_enqueue_script(
			'wl-meta-box-ingredient',
			WL_DIR_URL . 'js/dist/ingredients-meta-box.js',
			array( 'react', 'react-dom', 'wp-polyfill' ),
			WORDLIFT_VERSION,
			true
		);

		wp_localize_script(
			'wl-meta-box-ingredient',
			'_wlRecipeIngredientSettings',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wl-ingredient-nonce' ),
				'acNonce' => wp_create_nonce( 'wl-ac-ingredient-nonce' ),
				'l10n'    => array(
					'Looking for main ingredients...'   => _x( 'Looking for main ingredients...', 'Main Ingredient select', 'wordlift' ),
					'Type at least 3 characters to search...' => _x( 'Type at least 3 characters to search...', 'Main Ingredient select', 'wordlift' ),
					'No results found for your search.' => _x( 'No results found: try changing or removing some words.', 'Main Ingredient select', 'wordlift' ),
					"(don't change)"                    => _x( "(don't change)", 'Main Ingredient select', 'wordlift' ),
					'(unset)'                           => _x( '(unset)', 'Main Ingredient select', 'wordlift' ),
				),
			)
		);
	}
}
