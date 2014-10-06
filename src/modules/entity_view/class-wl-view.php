<?php

class WL_View {

	var $base_uri, $suffix, $title, $language, $graph;

	function __construct( $base_uri = null, $suffix = '.json', $title = 'rdfs:label', $language = 'en' ) {

		$this->base_uri = ( null === $base_uri ? wl_config_get_dataset_base_uri() : $base_uri );
		$this->suffix   = $suffix;
		$this->title    = $title;
		$this->language = $language;

		$id = get_query_var( WL_ENTITY_VIEW_ENTITY_ID_QUERY_VAR );

		$this->graph = wl_jsonld_load_remote( $this->base_uri . '/' . $id . $this->suffix );
	}


	function get_property( $name, $language = null ) {

		$value = wl_jsonld_get_property( $this->graph, $name, $language, $this->suffix );
		$value = str_ireplace( "\n", "<br/>", $value );
		return $value;

	}

	function echo_property( $name, $language = null ) {

		echo $this->get_property( $name, $language );

	}

} 