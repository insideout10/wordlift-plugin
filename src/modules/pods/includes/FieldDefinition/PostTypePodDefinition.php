<?php
namespace Wordlift\Modules\Pods\FieldDefinition;

class PostTypePodDefinition extends AbstractFieldDefiniton {

	public function register() {
		$that = $this;
		add_action(
			'init',
			function () use ( $that ) {
				$that->register_pod( $that->context->get_pod_name(), $that->context->get_pod_type(), $that->context );
			}
		);
	}
}
