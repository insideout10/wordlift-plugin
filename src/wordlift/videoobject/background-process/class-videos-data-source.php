<?php

namespace Wordlift\Videoobject\Background_Process;

use Wordlift\Common\Background_Process\Data_Source;

class Videos_Data_Source extends Data_Source {

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	public function __construct( $state_storage_key ) {
		parent::__construct( $state_storage_key );
		$this->log = \Wordlift_Log_Service::get_logger( get_class() );
	}

	public function next() {
		$this->log->debug( 'Received video data source index as ' . $this->get_state()->index );
		$this->log->debug( 'Count set to ' . $this->get_batch_size() );

		return get_posts(
			array(
				'fields'      => 'ids',
				'post_status' => 'any',
				'numberposts' => $this->get_batch_size(),
				'offset'      => $this->get_state()->index,
				/**
				* @return array<string> An array of supported post types for the video import procedure.
				* @since 3.31.4
				* Filter name : wl_videoobject_procedure_post_types
				*/
			'post_type'       => array_unique(
				apply_filters(
					'wl_videoobject_procedure_post_types',
					array(
						'post',
						'page',
					)
				)
			),
			)
		);
	}

	public function count() {
		return count(
			get_posts(
				array(
					'fields'      => 'ids',
					'numberposts' => - 1,
					'post_status' => 'any',
					/**
					* @return array<string> An array of supported post types for the video import procedure.
					* @since 3.31.4
					* Filter name : wl_videoobject_procedure_post_types
					*/
					'post_type'   => array_unique(
						apply_filters(
							'wl_videoobject_procedure_post_types',
							array(
								'post',
								'page',
							)
						)
					),
				)
			)
		);

	}

	public function get_batch_size() {
		// For now use only 5 in order to prevent exceeding api limit.
		return 5;
	}

}
