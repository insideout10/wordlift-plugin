<?php

namespace Wordlift\Sameas_Metabox;
/**
 * @since 3.29.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Task_Validator {

	/**
	 * @var \Wordlift_Configuration_Service
	 */
	private $configuration_service;

	public function __construct( $configuration_service ) {
		$this->configuration_service = $configuration_service;
	}


	/**
	 * @return bool
	 */
	public function is_cleanup_task_should_be_shown() {

		$local_dataset_uri = $this->configuration_service->get_dataset_uri();

		$posts = get_posts( array(
			'post_type'   => \Wordlift_Entity_Service::valid_entity_post_types(),
			'meta_query'  => array(
				array(
					'key'     => \Wordlift_Schema_Service::FIELD_SAME_AS,
					'value'   => $local_dataset_uri,
					'compare' => 'LIKE'
				)
			),
			'numberposts' => 1
		) );

		return count( $posts ) > 0;
	}

}


