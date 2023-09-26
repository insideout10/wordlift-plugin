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

		$context_type = $this->schema->get_context_type();

		switch ( $context_type ) {
			case Context::POST:
				return new PostTypeDefinition( $this->schema );
			case Context::TERM:
				return new TaxonomyDefinition( $this->schema );
			default:
				return new AllPodsDefiniton( $this->schema );
		}
	}

}
