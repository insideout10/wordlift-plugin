<?php

namespace Wordlift\Metabox\Field;

class Field_Url {

	public function __construct() {
	}

	public function render() {
		include dirname( __FILE__ ) . '/partials/field-url.php';
	}

}
