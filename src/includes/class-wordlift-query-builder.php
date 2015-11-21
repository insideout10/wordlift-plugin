<?php

/**
 * A builder to build SPARQL queries.
 *
 * @since 3.1.7
 */
class Wordlift_Query_Builder {

	/**
	 * The INSERT statement template.
	 *
	 * @since 3.1.7
	 */
	const INSERT = 'INSERT DATA { %s };';

	/**
	 * Tell the statement function to guess the value type (URI or value).
	 *
	 * @since 3.1.7
	 */
	const OBJECT_AUTO = - 1;

	/**
	 * Tell the statement function that the value is a URI.
	 *
	 * @since 3.1.7
	 */
	const OBJECT_URI = 0;

	/**
	 * Tell the statement function that the value is a value.
	 *
	 * @since 3.1.7
	 */
	const OBJECT_VALUE = 1;

	/**
	 * The RDFS type.
	 *
	 * @since 3.1.7
	 */
	const RDFS_TYPE_URI = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type';

	/**
	 * The schema.org/Person type.
	 *
	 * @since 3.1.7
	 */
	const SCHEMA_PERSON_URI = 'http://schema.org/Person';

	/**
	 * The RDF given name predicate.
	 *
	 * @since 3.1.7
	 */
	const SCHEMA_GIVEN_NAME_URI = 'http://schema.org/givenName';

	/**
	 * The RDF family name predicate.
	 *
	 * @since 3.1.7
	 */
	const SCHEMA_FAMILY_NAME_URI = 'http://schema.org/familyName';

	/**
	 * The RDF label.
	 *
	 * @since 3.1.7
	 */
	const RDFS_LABEL_URI = 'http://www.w3.org/2000/01/rdf-schema#label';

	/**
	 * Hold the template (INSERT or DELETE).
	 *
	 * @since 3.1.7
	 * @access private
	 * @var string $template The query template.
	 */
	private $template;

	/**
	 * An array of statements (in the form of subject, predicate, object).
	 *
	 * @since 3.1.7
	 * @access private
	 * @var array $statements An array of statements.
	 */
	private $statements = array();

	/**
	 * Set the query to INSERT.
	 *
	 * @since 3.1.7
	 * @return $this \Wordlift_Query_Builder The Query builder.
	 */
	public function insert() {

		$this->template = self::INSERT;

		return $this;
	}

	/**
	 * Add a statement.
	 *
	 * @since 3.1.7
	 *
	 * @param string $subject The subject of the statement (must be a URI).
	 * @param string $predicate The predicate (must be a URI).
	 * @param string $value The object, can be a URI or a value.
	 * @param int $object_type The object type, either a {@link OBJECT_URI} or a value {@link OBJECT_VALUE}. If set to {@link OBJECT_AUTO}, the Query builder will try to guess.
	 * @param string|null $data_type The data type (or null).
	 * @param string|null $language The language code (or null).
	 *
	 * @return $this \Wordlift_Query_Builder The Query builder.
	 */
	public function statement( $subject, $predicate, $value, $object_type = self::OBJECT_AUTO, $data_type = null, $language = null ) {

		// If no value has been provided, we don't set any statement.
		if ( empty( $value ) ) {
			return $this;
		}

		// Get the object type if set, otherwise try to guess it.
		$object_value_type = ( self::OBJECT_AUTO !== $object_type ?: $this->guess_object_type( $predicate ) );

		// Prepare the statement template.
		$template = '<%1$s> <%2$s> '
		            . ( self::OBJECT_URI === $object_value_type ? '<%3$s>' : '"%3$s"'
		                                                                     . ( isset( $data_type ) ? '^^%4$s' : '' )
		                                                                     . ( isset( $language ) ? '@%5$s' : '' ) );

		// Escape the subject, predicate and object.
		$escaped_subject   = $this->escape_uri( $subject );
		$escaped_predicate = $this->escape_uri( $predicate );
		$escaped_object    = ( self::OBJECT_URI === $object_value_type ? $this->escape_uri( $value ) : $this->escape_value( $value ) );

		// Prepare the statement and add it to the list of statements.
		$this->statements[] = sprintf( $template, $escaped_subject, $escaped_predicate, $escaped_object, $data_type, $language );

		return $this;
	}

	/**
	 * Build the query.
	 *
	 * @since 3.1.7
	 * @return string The query string.
	 */
	public function build() {

		return sprintf( $this->template, implode( ' . ', $this->statements ) );
	}

	/**
	 * Guess the statement object type.
	 *
	 * @since 3.1.7
	 *
	 * @param string $predicate The predicate.
	 *
	 * @return int {@link OBJECT_URI} if the Query builder thinks the object must be an URI, {@link OBJECT_VALUE} otherwise.
	 */
	private function guess_object_type( $predicate ) {

		switch ( $predicate ) {

			case self::RDFS_TYPE_URI:
				return self::OBJECT_URI;

			default:
		}

		return self::OBJECT_VALUE;
	}

	/**
	 * Escape a URI.
	 *
	 * @since 3.1.7
	 *
	 * @param string $uri The URI to escape.
	 *
	 * @return string The escaped URI.
	 */
	private function escape_uri( $uri ) {

		// Should we validate the IRI?
		// http://www.w3.org/TR/sparql11-query/#QSynIRI

		$escaped_uri = str_replace( '<', '\<', $uri );
		$escaped_uri = str_replace( '>', '\>', $escaped_uri );

		return $escaped_uri;
	}

	/**
	 * Escape a value.
	 *
	 * @since 3.1.7
	 *
	 * @param string $value The value to escape.
	 *
	 * @return string The escaped value.
	 */
	private function escape_value( $value ) {

		// see http://www.w3.org/TR/rdf-sparql-query/
		//    '\t'	U+0009 (tab)
		//    '\n'	U+000A (line feed)
		//    '\r'	U+000D (carriage return)
		//    '\b'	U+0008 (backspace)
		//    '\f'	U+000C (form feed)
		//    '\"'	U+0022 (quotation mark, double quote mark)
		//    "\'"	U+0027 (apostrophe-quote, single quote mark)
		//    '\\'	U+005C (backslash)

		$escaped_value = str_replace( '\\', '\\\\', $value );
		$escaped_value = str_replace( '\'', '\\\'', $escaped_value );
		$escaped_value = str_replace( '"', '\\"', $escaped_value );
		$escaped_value = str_replace( "\f", '\\f', $escaped_value );
		$escaped_value = str_replace( "\b", '\\b', $escaped_value );
		$escaped_value = str_replace( "\r", '\\r', $escaped_value );
		$escaped_value = str_replace( "\n", '\\n', $escaped_value );
		$escaped_value = str_replace( "\t", '\\t', $escaped_value );

		return $escaped_value;
	}

}
