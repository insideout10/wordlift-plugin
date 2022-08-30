<?php
/**
 * This file defines the rest controller for FAQ
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Faq
 */

namespace Wordlift\Faq;

use WP_REST_Request;

/**
 * Class Faq_Rest_Controller
 *
 * @package Wordlift\FAQ
 */
class Faq_Rest_Controller {
	const FAQ_META_KEY = '_wl_faq';
	/**
	 * Enumerations to determine the field to be deleted on
	 * the DELETE request in API.
	 */
	const QUESTION = 'question';
	const ANSWER   = 'answer';

	public static function register_routes() {
		add_action( 'rest_api_init', 'Wordlift\FAQ\FAQ_Rest_Controller::register_route_callback' );
	}

	public static function register_route_callback() {
		/**
		 * Specifies the list of arguments to be present for update request.
		 */
		$post_id_validation_settings   = array(
			'required'          => true,
			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			'validate_callback' => function ( $param, $request, $key ) {
				return is_numeric( $param );
			},
		);
		$faq_items_validation_settings = array(
			'required'          => true,
			// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			'validate_callback' => function ( $param, $request, $key ) {
				return is_array( $param ) && count( $param ) > 0;
			},
		);
		/**
		 * Array of args to be present in order to
		 * make create, update or delete request.
		 */
		$create_or_update_or_delete_args = array(
			'post_id'   => $post_id_validation_settings,
			'faq_items' => $faq_items_validation_settings,
		);

		/**
		 * Rest route for creating new faq item.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/faq',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => 'Wordlift\Faq\Faq_Rest_Controller::add_faq_items',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
				'args'                => $create_or_update_or_delete_args,
			)
		);
		/**
		 * Rest route for updating faq items.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/faq',
			array(
				'methods'             => \WP_REST_Server::EDITABLE,
				'callback'            => 'Wordlift\Faq\Faq_Rest_Controller::update_faq_items',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
				'args'                => $create_or_update_or_delete_args,
			)
		);
		/**
		 * Rest route for getting the faq items.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/faq/(?P<post_id>\d+)',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => 'Wordlift\Faq\Faq_Rest_Controller::get_faq_items',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
				'args'                => array( 'post_id' => $post_id_validation_settings ),
			)
		);
		/**
		 * Rest route for deleting faq item.
		 */
		register_rest_route(
			WL_REST_ROUTE_DEFAULT_NAMESPACE,
			'/faq',
			array(
				'methods'             => \WP_REST_Server::DELETABLE,
				'callback'            => 'Wordlift\Faq\Faq_Rest_Controller::delete_faq_items',
				'permission_callback' => function () {
					return current_user_can( 'publish_posts' );
				},
				'args'                => $create_or_update_or_delete_args,
			)
		);
	}

	/**
	 * Get all FAQ items for a post id.
	 *
	 * @param $request $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Result array, if post id is not given then error array is returned.
	 */
	public static function get_faq_items( $request ) {
		$data    = $request->get_params();
		$post_id = (int) $data['post_id'];

		return get_post_meta( $post_id, self::FAQ_META_KEY );
	}

	/**
	 * Update all FAQ items for a post id.
	 *
	 * @param $request $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Result array, if post id is not given then error array is returned.
	 */
	public static function update_faq_items( $request ) {
		$data      = $request->get_params();
		$post_id   = (int) $data['post_id'];
		$faq_items = (array) $data['faq_items'];
		foreach ( $faq_items as $faq_item ) {
			$previous_value = array(
				'question' => (string) $faq_item['previous_question_value'],
				'answer'   => (string) $faq_item['previous_answer_value'],
				'id'       => (int) $faq_item['id'],
			);
			$new_value      = array(
				'question' => (string) $faq_item['question'],
				'answer'   => (string) $faq_item['answer'],
				'id'       => (int) $faq_item['id'],
			);
			update_post_meta( $post_id, self::FAQ_META_KEY, $new_value, $previous_value );
		}

		return array(
			'status'  => 'success',
			'message' => __( 'Faq Items updated successfully', 'wordlift' ),
		);

	}

	/**
	 * Delete Faq items.
	 *
	 * @param $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Associative array whether the faq item is deleted or not.
	 */
	public static function delete_faq_items( $request ) {
		$post_data = $request->get_params();
		$post_id   = $post_data['post_id'];
		$faq_items = $post_data['faq_items'];
		foreach ( $faq_items as $faq_item ) {
			/**
			 * Note: the order of keys is important in order to delete it properly
			 * If the order change, delete operation will fail since it is converted
			 * in to a string when it was stored.
			 * we cant rely on client to post it in correct order, so we create an array
			 * in correct order.
			 */
			$deleted_faq_item = array(
				'question' => $faq_item['question'],
				'answer'   => $faq_item['answer'],
				'id'       => (int) $faq_item['id'],
			);
			/**
			 * If the field to be deleted is answer, then don't delete the faq item,
			 * if it is question then delete the faq item.
			 */
			if ( self::ANSWER === $faq_item['field_to_be_deleted'] ) {
				$previous_value = $deleted_faq_item;
				// then don't delete the faq item, set the answer as empty.
				$deleted_faq_item['answer'] = '';
				$new_value                  = $deleted_faq_item;
				update_post_meta( $post_id, self::FAQ_META_KEY, $new_value, $previous_value );
			} elseif ( self::QUESTION === $faq_item['field_to_be_deleted'] ) {
				/**
				 * If the question is deleted, then delete the faq item.
				 */
				delete_post_meta( $post_id, self::FAQ_META_KEY, $deleted_faq_item );
			}
		}

		/**
		 * We are returning only the first item id, since the user can select only one text at a time.
		 */
		return array(
			'status'  => 'success',
			'message' => __( 'Faq item successfully deleted.', 'wordlift' ),
		);
	}

	/**
	 * Insert or update FAQ items.
	 *
	 * @param $request WP_REST_Request $request {@link WP_REST_Request instance}.
	 *
	 * @return array Associative array whether the faq item is inserted or not
	 */
	public static function add_faq_items( $request ) {
		$post_data = $request->get_params();
		$post_id   = $post_data['post_id'];
		$faq_items = $post_data['faq_items'];
		foreach ( $faq_items as &$faq_item ) {
			// Add an identifier id to the faq item, it helps to prevent duplication problem.
			/**
			 * We are using time() and a random integer to prevent
			 * duplication problem.
			 */
			$faq_item['id'] = time() + wp_rand( 1, 100 ) + wp_rand( 1, 100 );
			add_post_meta( (int) $post_id, self::FAQ_META_KEY, $faq_item );
		}

		/**
		 * We are returning only the first item id, since the user can select only one text at a time.
		 */
		return array(
			'status'  => 'success',
			'message' => __( 'Question successfully added.', 'wordlift' ),
			'id'      => (int) $faq_items[0]['id'],
		);

	}
}
