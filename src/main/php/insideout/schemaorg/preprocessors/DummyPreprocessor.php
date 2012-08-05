<?php

class SchemaOrg_DummyPreprocessor implements SchemaOrg_IPreprocessor {
	
	public function supportsType( $type ) {
		return true;
	}

	public function process( &$properties ) {
		
	}

	public function __toString() {
		return "this is a DummyPreprocessor.";
	}

}

?>