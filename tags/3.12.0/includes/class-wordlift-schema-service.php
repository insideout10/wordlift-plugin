<?php
/**
 * Services: WordLift Schema Service.
 *
 * This file defines the Wordlift_Schema_Service class.
 *
 * @since   3.1.0
 * @package Wordlift
 */

/**
 * Provides constants and methods related to WordLift's schema.
 *
 * @since 3.1.0
 */
class Wordlift_Schema_Service {

	/**
	 * The 'location created' field name.
	 *
	 * @since 3.5.0
	 */
	const FIELD_LOCATION_CREATED = 'wl_location_created';

	/**
	 * The 'topic' field name.
	 *
	 * @since 3.5.0
	 */
	const FIELD_TOPIC = 'wl_topic';

	/**
	 * The 'author' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_AUTHOR = 'wl_author';

	/**
	 * The 'same as' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_SAME_AS = 'entity_same_as';

	/**
	 * The 'date start' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_DATE_START = 'wl_cal_date_start';

	/**
	 * The 'date end' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_DATE_END = 'wl_cal_date_end';

	/**
	 * The 'location' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_LOCATION = 'wl_location';

	/**
	 * The 'founder' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_FOUNDER = 'wl_founder';

	/**
	 * The 'knows' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_KNOWS = 'wl_knows';

	/**
	 * The 'birth date' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_BIRTH_DATE = 'wl_birth_date';

	/**
	 * The 'birth place' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_BIRTH_PLACE = 'wl_birth_place';

	/**
	 * The 'latitude' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_GEO_LATITUDE = 'wl_geo_latitude';

	/**
	 * The 'longitude' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_GEO_LONGITUDE = 'wl_geo_longitude';

	/**
	 * The 'streetAddress' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_ADDRESS = 'wl_address';

	/**
	 * The 'postOfficeBoxNumber' field name.
	 *
	 * @since 3.3.0
	 */
	const FIELD_ADDRESS_PO_BOX = 'wl_address_post_office_box';

	/**
	 * The 'postalCode' field name.
	 *
	 * @since 3.3.0
	 */
	const FIELD_ADDRESS_POSTAL_CODE = 'wl_address_postal_code';

	/**
	 * The 'addressLocality' field name.
	 *
	 * @since 3.3.0
	 */
	const FIELD_ADDRESS_LOCALITY = 'wl_address_locality';
	/**
	 * The 'addressRegion' field name.
	 *
	 * @since 3.3.0
	 */
	const FIELD_ADDRESS_REGION = 'wl_address_region';

	/**
	 * The 'addressCountry' field name.
	 *
	 * @since 3.3.0
	 */
	const FIELD_ADDRESS_COUNTRY = 'wl_address_country';

	/**
	 * The 'entity type' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_ENTITY_TYPE = 'wl_entity_type_uri';

	/**
	 * The 'email' field name.
	 *
	 * @since 3.2.0
	 */
	const FIELD_EMAIL = 'wl_email';

	/**
	 * The 'affiliation' field name.
	 *
	 * @since 3.2.0
	 */
	const FIELD_AFFILIATION = 'wl_affiliation';

	/**
	 * The 'telephone' field name.
	 *
	 * @since 3.8.0
	 */
	const FIELD_TELEPHONE = 'wl_schema_telephone';

	/**
	 * The 'legalName' field name.
	 *
	 * @since 3.12.0
	 */
	const FIELD_LEGAL_NAME = 'wl_schema_legal_name';

	/**
	 * The 'URI' data type name.
	 *
	 * @since 3.1.0
	 */
	const DATA_TYPE_URI = 'uri';

	/**
	 * The 'date' data type name.
	 *
	 * @since 3.1.0
	 */
	const DATA_TYPE_DATE = 'date';

	/**
	 * The 'double' data type name.
	 *
	 * @since 3.1.0
	 */
	const DATA_TYPE_DOUBLE = 'double';

	/**
	 * The 'string' data type name.
	 *
	 * @since 3.1.0
	 */
	const DATA_TYPE_STRING = 'string';

	/**
	 * The 'integer' data type name.
	 *
	 * @since 3.1.0
	 */
	const DATA_TYPE_INTEGER = 'int';

	/**
	 * The 'boolean' data type name.
	 *
	 * @since 3.1.0
	 */
	const DATA_TYPE_BOOLEAN = 'bool';

	/**
	 * The schema.org Event type URI.
	 *
	 * @since 3.1.0
	 */
	const SCHEMA_EVENT_TYPE = 'http://schema.org/Event';

	/**
	 * The Schema service singleton instance.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Schema_Service $instance The Schema service singleton instance.
	 */
	private static $instance;

	/**
	 * WordLift's schema.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var array $schema WordLift's schema.
	 */
	private $schema;

	/**
	 * The Log service.
	 *
	 * @since  3.1.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Wordlift_Schema_Service constructor.
	 *
	 * @since 3.1.0
	 */
	public function __construct() {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Schema_Service' );

		// Create a singleton instance of the Schema service, useful to provide static functions to global functions.
		self::$instance = $this;

		// Set the taxonomy data.
		// Note: parent types must be defined before child types.
		$this->schema = array(
			'thing'         => $this->get_thing_schema(),
			'creative-work' => $this->get_creative_work_schema(),
			'event'         => $this->get_event_schema(),
			'organization'  => $this->get_organization_schema(),
			'person'        => $this->get_person_schema(),
			'place'         => $this->get_place_schema(),
			'localbusiness' => $this->get_local_business_schema(),
		);

	}

	/**
	 * Get a reference to the Schema service.
	 *
	 * @since 3.1.0
	 *
	 * @return Wordlift_Schema_Service A reference to the Schema service.
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Get the properties for a field with the specified key. The key is used as
	 * meta key when the field's value is stored in WordPress meta data table.
	 *
	 * @since 3.6.0
	 *
	 * @param string $key The field's key.
	 *
	 * @return null|array An array of field's properties or null if the field is not found.
	 */
	public function get_field( $key ) {

		// Parse each schema's fields until we find the one we're looking for, then
		// return its properties.
		foreach ( $this->schema as $_ => $schema ) {

			if ( ! isset( $schema['custom_fields'] ) ) {
				break;
			}

			foreach ( $schema['custom_fields'] as $field => $props ) {
				if ( $key === $field ) {
					return $props;
				}
			}
		}

		return null;
	}

	/**
	 * Get the WordLift's schema.
	 *
	 * @param string $name The schema name.
	 *
	 * @return array|null An array with the schema configuration or NULL if the schema is not found.
	 *
	 * @since 3.1.0
	 */
	public function get_schema( $name ) {

		// Check if the schema exists and, if not, return NULL.
		if ( ! isset( $this->schema[ $name ] ) ) {
			return null;
		}

		// Return the requested schema.
		return $this->schema[ $name ];
	}

	/**
	 * Get the WordLift's schema trough schema type uri.
	 *
	 * @param string $uri The schema uri.
	 *
	 * @return array|null An array with the schema configuration or NULL if the schema is not found.
	 *
	 * @since 3.3.0
	 */
	public function get_schema_by_uri( $uri ) {

		foreach ( $this->schema as $name => $schema ) {
			if ( $schema['uri'] === $uri ) {
				return $schema;
			}
		}

		return null;
	}

	/**
	 * Get the 'thing' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_thing_schema() {

		return array(
			'css_class'     => 'wl-thing',
			'uri'           => 'http://schema.org/Thing',
			'same_as'       => array( '*' ),
			// set as default.
			'custom_fields' => array(
				self::FIELD_SAME_AS                            => array(
					'predicate'   => 'http://schema.org/sameAs',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Thing',
					'constraints' => array(
						'cardinality' => INF,
					),
					// We need a custom metabox.
					'input_field' => 'sameas',
				),
				// Add the schema:url property.
				Wordlift_Schema_Url_Property_Service::META_KEY => Wordlift_Schema_Url_Property_Service::get_instance()
				                                                                                      ->get_compat_definition(),
			),
			// {{sameAs}} not present in the microdata template,
			// because it is treated separately in *wl_content_embed_item_microdata*
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

	}

	/**
	 * Get the 'creative work' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_creative_work_schema() {

		$schema = array(
			'label'         => 'CreativeWork',
			'description'   => 'A creative work (or a Music Album).',
			'parents'       => array( 'thing' ),
			// Give term slug as parent.
			'css_class'     => 'wl-creative-work',
			'uri'           => 'http://schema.org/CreativeWork',
			'same_as'       => array(
				'http://schema.org/MusicAlbum',
				'http://schema.org/Product',
			),
			'custom_fields' => array(
				self::FIELD_AUTHOR => array(
					'predicate'   => 'http://schema.org/author',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type'    => array( 'Person', 'Organization' ),
						'cardinality' => INF,
					),
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$thing_schema            = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $thing_schema['custom_fields'] );

		return $schema;
	}

	/**
	 * Get the 'event' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_event_schema() {

		$schema = array(
			'label'         => 'Event',
			'description'   => 'An event . ',
			'parents'       => array( 'thing' ),
			'css_class'     => 'wl-event',
			'uri'           => self::SCHEMA_EVENT_TYPE,
			'same_as'       => array( 'http://dbpedia.org/ontology/Event' ),
			'custom_fields' => array(
				self::FIELD_DATE_START => array(
					'predicate'   => 'http://schema.org/startDate',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:datetime',
					'constraints' => '',
				),
				self::FIELD_DATE_END   => array(
					'predicate'   => 'http://schema.org/endDate',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:datetime',
					'constraints' => '',
				),
				self::FIELD_LOCATION   => array(
					'predicate'   => 'http://schema.org/location',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/PostalAddress',
					'constraints' => array(
						'uri_type'    => array( 'Place', 'LocalBusiness' ),
						'cardinality' => INF,
					),
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$thing_schema            = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $thing_schema['custom_fields'] );

		return $schema;
	}

	/**
	 * Get the 'organization' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_organization_schema() {

		$schema = array(
			'label'         => 'Organization',
			'description'   => 'An organization, including a government or a newspaper.',
			'parents'       => array( 'thing' ),
			'css_class'     => 'wl-organization',
			'uri'           => 'http://schema.org/Organization',
			'same_as'       => array(
				'http://rdf.freebase.com/ns/organization.organization',
				'http://rdf.freebase.com/ns/government.government',
				'http://schema.org/Newspaper',
			),
			'custom_fields' => array(
				self::FIELD_LEGAL_NAME          => array(
					'predicate'   => 'http://schema.org/legalName',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
				),
				self::FIELD_FOUNDER             => array(
					'predicate'   => 'http://schema.org/founder',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type'    => 'Person',
						'cardinality' => INF,
					),
				),
				self::FIELD_ADDRESS             => array(
					'predicate'   => 'http://schema.org/streetAddress',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_PO_BOX      => array(
					'predicate'   => 'http://schema.org/postOfficeBoxNumber',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_POSTAL_CODE => array(
					'predicate'   => 'http://schema.org/postalCode',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_LOCALITY    => array(
					'predicate'   => 'http://schema.org/addressLocality',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_REGION      => array(
					'predicate'   => 'http://schema.org/addressRegion',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_COUNTRY     => array(
					'predicate'   => 'http://schema.org/addressCountry',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_EMAIL               => array(
					'predicate'   => 'http://schema.org/email',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
				),
				self::FIELD_TELEPHONE           => array(
					'predicate'   => 'http://schema.org/telephone',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$thing_schema            = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $thing_schema['custom_fields'] );

		return $schema;
	}

	/**
	 * Get the 'person' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_person_schema() {

		$schema = array(
			'label'         => 'Person',
			'description'   => 'A person (or a music artist).',
			'parents'       => array( 'thing' ),
			'css_class'     => 'wl-person',
			'uri'           => 'http://schema.org/Person',
			'same_as'       => array(
				'http://rdf.freebase.com/ns/people.person',
				'http://rdf.freebase.com/ns/music.artist',
				'http://dbpedia.org/class/yago/LivingPeople',
			),
			'custom_fields' => array(
				self::FIELD_KNOWS       => array(
					'predicate'   => 'http://schema.org/knows',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type'    => 'Person',
						'cardinality' => INF,
					),
				),
				self::FIELD_BIRTH_DATE  => array(
					'predicate'   => 'http://schema.org/birthDate',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:date',
					'constraints' => '',
				),
				self::FIELD_BIRTH_PLACE => array(
					'predicate'   => 'http://schema.org/birthPlace',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Place',
					'constraints' => array(
						'uri_type' => 'Place',
					),
				),
				self::FIELD_AFFILIATION => array(
					'predicate'   => 'http://schema.org/affiliation',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Organization',
					'constraints' => array(
						'uri_type'    => array(
							'Organization',
							'LocalBusiness',
						),
						'cardinality' => INF,
					),
				),
				self::FIELD_EMAIL       => array(
					'predicate'   => 'http://schema.org/email',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => array(
						'cardinality' => INF,
					),
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$thing_schema            = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $thing_schema['custom_fields'] );

		return $schema;

	}

	/**
	 * Get the 'place' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_place_schema() {

		$schema = array(
			'label'         => 'Place',
			'description'   => 'A place.',
			'parents'       => array( 'thing' ),
			'css_class'     => 'wl-place',
			'uri'           => 'http://schema.org/Place',
			'same_as'       => array(
				'http://rdf.freebase.com/ns/location.location',
				'http://www.opengis.net/gml/_Feature',
			),
			'custom_fields' => array(
				self::FIELD_GEO_LATITUDE        => array(
					'predicate'   => 'http://schema.org/latitude',
					'type'        => self::DATA_TYPE_DOUBLE,
					'export_type' => 'xsd:double',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'coordinates',
				),
				self::FIELD_GEO_LONGITUDE       => array(
					'predicate'   => 'http://schema.org/longitude',
					'type'        => self::DATA_TYPE_DOUBLE,
					'export_type' => 'xsd:double',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'coordinates',
				),
				self::FIELD_ADDRESS             => array(
					'predicate'   => 'http://schema.org/streetAddress',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_PO_BOX      => array(
					'predicate'   => 'http://schema.org/postOfficeBoxNumber',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_POSTAL_CODE => array(
					'predicate'   => 'http://schema.org/postalCode',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_LOCALITY    => array(
					'predicate'   => 'http://schema.org/addressLocality',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_REGION      => array(
					'predicate'   => 'http://schema.org/addressRegion',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
				self::FIELD_ADDRESS_COUNTRY     => array(
					'predicate'   => 'http://schema.org/addressCountry',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					// To build custom metabox.
					'input_field' => 'address',
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$thing_schema            = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $thing_schema['custom_fields'] );

		return $schema;
	}

	/**
	 * Get the 'local business' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_local_business_schema() {

		$schema = array(
			'label'         => 'LocalBusiness',
			'description'   => 'A local business.',
			'parents'       => array( 'place', 'organization' ),
			'css_class'     => 'wl-local-business',
			'uri'           => 'http://schema.org/LocalBusiness',
			'same_as'       => array(
				'http://rdf.freebase.com/ns/business/business_location',
				'https://schema.org/Store',
			),
			'custom_fields' => array(),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the place and organization schema.
		$place_schema            = $this->get_place_schema();
		$organization_schema     = $this->get_organization_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $place_schema['custom_fields'], $organization_schema['custom_fields'] );

		return $schema;
	}

}
