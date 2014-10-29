<?php

// Load JsonPath.
if ( ! class_exists( 'JSONPath' ) ) {
	require_once( 'JSONPath/JSONPath.php' );
	require_once( 'JSONPath/JSONPathLexer.php' );
	require_once( 'JSONPath/JSONPathException.php' );
	require_once( 'JSONPath/Filters/AbstractFilter.php' );
	require_once( 'JSONPath/Filters/IndexesFilter.php' );
	require_once( 'JSONPath/Filters/IndexFilter.php' );
	require_once( 'JSONPath/Filters/QueryMatchFilter.php' );
	require_once( 'JSONPath/Filters/QueryResultFilter.php' );
	require_once( 'JSONPath/Filters/RecursiveFilter.php' );
	require_once( 'JSONPath/Filters/SliceFilter.php' );
	require_once( 'JSONPath/Filters/LoadFilter.php' );
}


use Flow\JSONPath\JSONPath;

class WL_View {

	var $base_uri, $suffix, $title, $language, $graph;

	var $json_path;

	function __construct( $base_uri = null, $suffix = '.json', $title = 'rdfs:label', $language = 'en' ) {

		$this->base_uri = ( null === $base_uri ? wl_config_get_dataset_base_uri() : $base_uri );
		$this->suffix   = $suffix;
		$this->title    = $title;
		$this->language = $language;

	}

	function load( $path = null ) {

		// If the path is empty, load the resource from the query string variable.
		if ( empty( $path ) ) {
			$path = get_query_var( WL_ENTITY_VIEW_ENTITY_ID_QUERY_VAR );
		}

		// If a base URI has been set, append the path, otherwise use the path.
		$url = ( ! empty( $this->base_uri ) ? $this->base_uri . '/' . $path : $path );

//		wp_die( $this->base_uri . '/' . $id . $this->suffix );

//		wp_die( $url );
		$this->graph     = wl_jsonld_load_remote( $url . $this->suffix );
		$this->json_path = new JSONPath( $this->graph );

	}

	function get_property( $name, $language = null, $index = 0 ) {


//		echo( "name: $name\n" );
		$values = $this->json_path->find( $this->expand( $name ) );
		return $values[$index];

//		$value = wl_jsonld_get_property( $this->graph, $name, $language, $this->suffix, $index );
//		return $value;

	}

	function expand( $expr ) {

		foreach ( wl_prefixes_list() as $item ) {
			$prefix    = $item['prefix'];
			$namespace = $item['namespace'];
			$expr      = preg_replace( "/(['\"])$prefix:/", "\${1}$namespace", $expr );
			$expr      = preg_replace( "/\\.$prefix:([^.]+)\\./", "['$namespace\${1}']", $expr );
		}


		return $expr;
	}

	function get_property_html( $name, $language = null, $index = 0 ) {

		return esc_html( $this->get_property( $name, $language, $index ) );

	}

	function get_first_property_html( $name ) {

		return esc_html( $this->get_property( $name, null, 0 ) );

	}

	function echo_property( $name, $language = null, $index = 0 ) {

		echo $this->get_property( $name, $language, $index );

	}

} 