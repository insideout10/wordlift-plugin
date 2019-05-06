<?php
/**
 * Pages: Batch analysis admin page.
 *
 * Handles the WordLift batch analysis admin page.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Batch_analysis_page} class.
 *
 * @since      3.14.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Batch_Analysis_Page extends Wordlift_Admin_Page {

	/**
	 * The {@link Wordlift_Batch_analysis_Service} instance.
	 *
	 * @since 3.14.0
	 *
	 * @var \Wordlift_Batch_Analysis_Service $batch_analysis_service The {@link Wordlift_Batch_analysis_Service} instance.
	 */
	public $batch_analysis_service;

	/**
	 * The {@link Wordlift_Batch_Analysis_page} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift_Batch_Analysis_Service $batch_analysis_service The {@link Wordlift_Batch_analysis_Service} instance.
	 */
	public function __construct( $batch_analysis_service ) {

		$this->batch_analysis_service = $batch_analysis_service;

	}

	/**
	 * @inheritdoc
	 */
	function get_parent_slug() {

		return 'wl_admin_menu';
	}

	/**
	 * @inheritdoc
	 */
	function get_capability() {

		return 'manage_options';
	}

	/**
	 * @inheritdoc
	 */
	function get_page_title() {

		return __( 'Batch Analysis', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_title() {

		return __( 'Batch Analysis', 'wordlift' );
	}

	/**
	 * @inheritdoc
	 */
	function get_menu_slug() {

		return 'wl_batch_analysis_menu';
	}

	/**
	 * @inheritdoc
	 */
	function get_partial_name() {

		return 'wordlift-admin-batch-analysis-page.php';
	}

}
