<?php

namespace Wordlift\Modules\Pods\FieldDefinition;

use Wordlift\Modules\Pods\Context;
use Wordlift\Modules\Pods\Schema;

class FieldDefinitionFactory {

	/**
	 * @var $schema Schema
	 */
	private $schema;

	public function __construct( $schema ) {
		$this->schema = $schema;
	}


	public function get_field_definition() {

		// For now we are registering all the pods.
		return new AllPodsDefiniton( $this->schema );
	}


}
