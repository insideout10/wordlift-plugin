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

/**
 * The WL_View class provides access to remote JSON-LD resources.
 *
 * @since 3.0.0
 */
class WL_View {

	var $base_uri, $suffix, $title, $language, $graph, $url;

	var $json_path;

	/**
	 * Create an instance of WL_View.
	 *
	 * @since 3.0.0
	 *
	 * @uses ::wl_configuration_get_redlink_dataset_uri to get the default dataset URI.
	 *
	 * @param string $base_uri The base URI for resources, default the WordLift dataset.
	 * @param string $suffix The suffix to append to URI in order to load the JSON-LD file, default *.json*.
	 * @param string $title The predicate used to set the title of the page, default *rdfs:label*.
	 * @param string $language The language for values, default *en*.
	 */
	function __construct( $base_uri = null, $suffix = '.json', $title = 'rdfs:label', $language = 'en' ) {

		// Set the instance variables.
		$this->base_uri = ( null === $base_uri ? wl_configuration_get_redlink_dataset_uri() : $base_uri );
		$this->suffix   = $suffix;
		$this->title    = $title;
		$this->language = $language;

		wl_write_log( "[ base URI :: $this->base_uri ][ suffix :: $this->suffix ][ title :: $this->title ][ language :: $this->language ]" );

		add_filter( 'wp_title', array( $this, 'filter_page_title' ), 10, 2 );
	}



	/**
	 * Set the page title using the provided title.
	 *
	 * @since 3.0.0
	 *
	 * @see wp_title the WordPress filter that calls this method.
	 * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/wp_title
	 *
	 * @param string $title The existing title.
	 * @param string $sep   The title separator.
	 * @return string The new title.
	 */
	function filter_page_title( $title, $sep ) {

		$expr       = "$['$this->title'][?(@.@language=='$this->language')].@value";
		$view_title = $this->get_first_property_html( $expr );

		return $view_title . " $sep $title";

	}

	/**
	 * Load the JSON-LD at the specified path (the path is appended to the *base_uri*). If the path is empty or null
	 * it is loaded from the query variables (via WordPress).
	 *
	 * @since 3.0.0
	 *
	 * @uses ::wl_configuration_get_redlink_dataset_uri to get the default dataset URI.
	 * @uses ::wl_jsonld_load_remote to load a remote JSON-LD file.
	 *
	 * @param string $path The entity path.
	 */
	function load( $path = null ) {

		// If the path is empty, load the resource from the query string variable.
		if ( empty( $path ) ) {
			$path = get_query_var( WL_ENTITY_VIEW_ENTITY_ID_QUERY_VAR );
		}

		// If a base URI has been set, append the path, otherwise use the path.
		$this->url = ( ! empty( $this->base_uri )
			? $this->base_uri . ( '/' !== substr( $this->base_uri, - 1, 1 ) ? '/' : '' ) . $path
			: $path );

		$this->graph     = wl_jsonld_load_remote( $this->url . $this->suffix );
		$this->json_path = new JSONPath( $this->graph );

	}

	/**
	 * Get the property with the specified name. The value at the specified index will be returned.
	 *
	 * @since 3.0.0
	 *
	 * @uses ::expand to expand the property name.
	 *
	 * @param string $name          The property name.
	 * @param int $index            The value index (default NULL, returns an array of properties).
	 *
	 * @return string|array The property value.
	 */
	function get_property( $name, $index = NULL ) {


		$values = $this->json_path->find( $this->expand( $name ) );

		wl_write_log( "[ name :: $name ][ index :: $index ][ value :: " . var_export( $values, true ) . " ]" );


		return ( NULL !== $index ? $values[ $index ] : $values );

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

	function get_property_html( $name, $index = 0 ) {

		return esc_html( $this->get_property( $name, $index ) );

	}

	/**
	 * Gets the first property with the provided name.
	 *
	 * @since 3.0.0
	 *
	 * @uses ::get_first_property to get the first property value.
	 *
	 * @param string $name The predicate name.
	 *
	 * @return string The value.
	 */
	function get_first_property_html( $name ) {

		return esc_html( $this->get_first_property( $name ) );

	}

	/**
	 * Gets the first property with the provided name and returns it HTML escaped.
	 *
	 * @since 3.0.0
	 *
	 * @uses ::get_property to get the property value.
	 *
	 * @param string $name The predicate name.
	 *
	 * @return string The value.
	 */
	function get_first_property( $name ) {

		return $this->get_property( $name, 0 );

	}


	function echo_property( $name, $index = 0 ) {

		echo $this->get_property( $name, $index );

	}

	/**
	 * @since 3.0.0
	 *
	 * @param $name
	 *
	 * @return string
	 */
	function get_property_localized( $name ) {

		return $this->get_property( $this->localize_expression( $name ) );

	}

	function get_first_property_localized( $name ) {

		return $this->get_first_property( $this->localize_expression( $name ) );

	}

	/**
	 * @since 3.0.0
	 *
	 * @param $name
	 *
	 * @return string
	 */
	function get_first_property_html_localized( $name ) {

		return esc_html( $this->get_first_property_localized( $name ) );

	}

	/**
	 * Localizes the expression using the language set when creating the view instance.
	 *
	 * @since 3.0.0
	 *
	 * @param string $name The expression to localize.
	 *
	 * @return string The localized expression.
	 */
	function localize_expression( $name ) {

		return $name . "[?(@.@language=='$this->language')].@value";

	}


} 