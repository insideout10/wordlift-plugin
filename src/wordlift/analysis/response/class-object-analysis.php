<?php

namespace Wordlift\Analysis\Response;

abstract class Object_Analysis {

	/**
	 * The analysis response json.
	 *
	 * @since 3.21.5
	 * @access private
	 * @var mixed $json Holds the analysis response json.
	 */
	protected $json;

	public function __construct( $json ) {
		$this->json = $json;
	}


	/**
	 * Switches remote entities, i.e. entities with id outside the local dataset, to local entities.
	 *
	 * The function takes all the entities that have an id which is not local. For each remote entity, a list of URIs
	 * is built comprising the entity id and the sameAs. Then a query is issued in the local database to find potential
	 * matches from the local vocabulary.
	 *
	 * If found, the entity id is swapped with the local id and the remote id is added to the sameAs.
	 *
	 * @return Analysis_Response_Ops The current Analysis_Response_Ops instance.
	 */
	abstract public function make_entities_local();

	/**
	 * Add occurrences by parsing the provided html content.
	 *
	 * @param string $content The html content with annotations.
	 *
	 * @return Analysis_Response_Ops The {@link Analysis_Response_Ops} instance.
	 *
	 * @since 3.23.7 refactor the regex pattern to take into account that there might be css classes between textannotation
	 *  and disambiguated.
	 *
	 * @link https://github.com/insideout10/wordlift-plugin/issues/1001
	 */
	abstract public function add_occurrences( $content );

	/**
	 * Add local entities
	 *
	 * @return Analysis_Response_Ops The {@link Analysis_Response_Ops} instance.
	 *
	 * @since 3.27.6
	 *
	 * @link https://github.com/insideout10/wordlift-plugin/issues/1178
	 */
	abstract public function add_local_entities();


	/**
	 * Return the JSON response.
	 *
	 * @return mixed The JSON response.
	 * @since 3.24.2
	 */
	public function get_json() {

		return $this->json;
	}

	/**
	 * Get the string representation of the JSON.
	 *
	 * @return false|string The string representation or false in case of error.
	 */
	public function to_string() {

		// Add the `JSON_UNESCAPED_UNICODE` only for PHP 5.4+.
		$options = ( version_compare( PHP_VERSION, '5.4', '>=' )
			? 256 : 0 );

		return wp_json_encode( $this->json, $options );
	}


	/**
	 * Should return the local entity array.
	 * @param $uri
	 *
	 * @return array | bool Associative array or false
	 */
	abstract public function get_local_entity( $uri);

}