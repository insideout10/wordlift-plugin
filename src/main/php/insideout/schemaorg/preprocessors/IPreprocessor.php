<?php

interface SchemaOrg_IPreprocessor {

	public function supportsType( $type );
	public function process( &$properties );
	
}

?>