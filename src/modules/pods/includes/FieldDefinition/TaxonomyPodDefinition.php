<?php
namespace Wordlift\Modules\Pods\FieldDefinition;

class TaxonomyPodDefinition extends AbstractFieldDefiniton {

	public function register() {
		$that = $this;
		add_action(
			'setup_theme',
			function () use ( $that ) {
				$that->register_pod( $that->context->get_pod_name(), $that->context->get_pod_type(), $that->context );
			}
		);
	}
}
