<?php

namespace Wordlift\Modules\Pods\FieldDefinition;

interface FieldDefinition {

	/**
	 * Register the fields for the provided context in constructor.
	 *
	 * @return void
	 */
	public function register();

}

