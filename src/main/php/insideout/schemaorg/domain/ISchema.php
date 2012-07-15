<?php

interface SchemaOrg_ISchema {

	public function getType();
	public function hasProperty($name);
	public function getProperty($name);

}

?>