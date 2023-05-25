<?php

namespace Wordlift\Modules\Pods\FieldDefinition;

class TaxonomyDefinition extends AbstractFieldDefiniton {

	public function register() {
		$that = $this;
		add_action(
			'setup_theme',
			function () use ( $that ) {
				$context = $that->schema->get();
				$that->register_pod( $context->get_pod_name(), $context->get_pod_type(), $context );
			}
		);
	}
}
