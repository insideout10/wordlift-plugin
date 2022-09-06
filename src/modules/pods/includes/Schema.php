<?php

namespace Wordlift\Modules\Pods;

class Schema {

	public function get() {
		// we need to identify the context to filter the results.
		$schema_classes = \Wordlift_Schema_Service::get_instance();
		return array( 'Person' => $schema_classes->get_schema('person') );

	}


}