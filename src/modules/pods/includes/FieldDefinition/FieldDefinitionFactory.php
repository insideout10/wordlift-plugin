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

		$context = $this->schema->get();
		return new AllPodsDefiniton( $context );

		if ( Context::ADMIN_AJAX === $context->get_object_type() ) {
			return new AllPodsDefiniton( $context );
		} elseif ( Context::POST === $context->get_object_type() ) {
			return new PostTypePodDefinition( $context );
		} elseif ( Context::TERM === $context->get_object_type() ) {
			return new TaxonomyPodDefinition( $context );
		} else {
			return new NoFieldDefintion( $context );
		}
	}


}
