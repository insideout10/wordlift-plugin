<?php

/**
 * Provides constants and methods related to WordLift's schema.
 *
 * @since 3.1.0
 */
class Wordlift_Schema_Service {

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
	 * The 'address' field name.
	 *
	 * @since 3.1.0
	 */
	const FIELD_ADDRESS = 'wl_address';


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
	 * Get the WordLift's schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	public function get_schema() {

		// Set the taxonomy data.
		// Note: parent types must be defined before child types.
		return array(
			'thing'         => $this->get_thing_schema(),
			'creative-work' => $this->get_creative_work_schema(),
			'event'         => $this->get_event_schema(),
			'organization'  => $this->get_organization_schema(),
			'person'        => $this->get_person_schema(),
			'place'         => $this->get_place_schema(),
			'localbusiness' => $this->get_local_business_schema()
		);

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
			'css'                => 'wl-thing',
			'uri'                => 'http://schema.org/Thing',
			'same_as'            => array( '*' ), // set as default.
			'custom_fields'      => array(
				self::FIELD_SAME_AS => array(
					'predicate'   => 'http://schema.org/sameAs',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Thing',
					'constraints' => array(
						'cardinality' => INF
					),
					'input_field' => 'sameas'   // we need a custom metabox
				)
			),
			// {{sameAs}} not present in the microdata template,
			// because it is treated separately in *wl_content_embed_item_microdata*
			'microdata_template' => '',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
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

		return array(
			'label'              => 'CreativeWork',
			'description'        => 'A creative work (or a Music Album).',
			'parents'            => array( 'thing' ), // give term slug as parent
			'css'                => 'wl-creative-work',
			'uri'                => 'http://schema.org/CreativeWork',
			'same_as'            => array(
				'http://schema.org/MusicAlbum',
				'http://schema.org/Product'
			),
			'custom_fields'      => array(
				self::FIELD_AUTHOR => array(
					'predicate'   => 'http://schema.org/author',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type'    => array( 'Person', 'Organization' ),
						'cardinality' => INF
					)
				),
			),
			'microdata_template' => '{{author}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		);

	}

	/**
	 * Get the 'event' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_event_schema() {

		return array(
			'label'              => 'Event',
			'description'        => 'An event.',
			'parents'            => array( 'thing' ),
			'css'                => 'wl-event',
			'uri'                => 'http://schema.org/Event',
			'same_as'            => array( 'http://dbpedia.org/ontology/Event' ),
			'custom_fields'      => array(
				self::FIELD_DATE_START => array(
					'predicate'   => 'http://schema.org/startDate',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:date',
					'constraints' => ''
				),
				self::FIELD_DATE_END   => array(
					'predicate'   => 'http://schema.org/endDate',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:date',
					'constraints' => ''
				),
				self::FIELD_LOCATION   => array(
					'predicate'   => 'http://schema.org/location',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/PostalAddress',
					'constraints' => array(
						'uri_type'    => 'Place',
						'cardinality' => INF
					)
				)
			),
			'microdata_template' =>
				'{{startDate}}
                                {{endDate}}
                                {{location}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		);

	}

	/**
	 * Get the 'organization' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_organization_schema() {

		return array(
			'label'              => 'Organization',
			'description'        => 'An organization, including a government or a newspaper.',
			'parents'            => array( 'thing' ),
			'css'                => 'wl-organization',
			'uri'                => 'http://schema.org/Organization',
			'same_as'            => array(
				'http://rdf.freebase.com/ns/organization.organization',
				'http://rdf.freebase.com/ns/government.government',
				'http://schema.org/Newspaper'
			),
			'custom_fields'      => array(
				self::FIELD_FOUNDER => array(
					'predicate'   => 'http://schema.org/founder',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type'    => 'Person',
						'cardinality' => INF
					)
				),
			),
			'microdata_template' => '{{founder}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		);

	}

	/**
	 * Get the 'person' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_person_schema() {

		return array(
			'label'              => 'Person',
			'description'        => 'A person (or a music artist).',
			'parents'            => array( 'thing' ),
			'css'                => 'wl-person',
			'uri'                => 'http://schema.org/Person',
			'same_as'            => array(
				'http://rdf.freebase.com/ns/people.person',
				'http://rdf.freebase.com/ns/music.artist',
				'http://dbpedia.org/class/yago/LivingPeople'
			),
			'custom_fields'      => array(
				self::FIELD_KNOWS       => array(
					'predicate'   => 'http://schema.org/knows',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type'    => 'Person',
						'cardinality' => INF
					)
				),
				self::FIELD_BIRTH_DATE  => array(
					'predicate'   => 'http://schema.org/birthDate',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:date',
					'constraints' => ''
				),
				self::FIELD_BIRTH_PLACE => array(
					'predicate'   => 'http://schema.org/birthPlace',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Place',
					'constraints' => array(
						'uri_type' => 'Place'
					)
				)
			),
			'microdata_template' =>
				'{{birthDate}}
                            {{birthPlace}}
                            {{knows}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		);

	}

	/**
	 * Get the 'place' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_place_schema() {

		return array(
			'label'              => 'Place',
			'description'        => 'A place.',
			'parents'            => array( 'thing' ),
			'css'                => 'wl-place',
			'uri'                => 'http://schema.org/Place',
			'same_as'            => array(
				'http://rdf.freebase.com/ns/location.location',
				'http://www.opengis.net/gml/_Feature'
			),
			'custom_fields'      => array(
				self::FIELD_GEO_LATITUDE  => array(
					'predicate'   => 'http://schema.org/latitude',
					'type'        => self::DATA_TYPE_DOUBLE,
					'export_type' => 'xsd:double',
					'constraints' => '',
					'input_field' => 'coordinates'   // to build custom metabox
				),
				self::FIELD_GEO_LONGITUDE => array(
					'predicate'   => 'http://schema.org/longitude',
					'type'        => self::DATA_TYPE_DOUBLE,
					'export_type' => 'xsd:double',
					'constraints' => '',
					'input_field' => 'coordinates'   // to build custom metabox
				),
				self::FIELD_ADDRESS       => array(
					'predicate'   => 'http://schema.org/address',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => ''
				)
			),
			'microdata_template' =>
				'<span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
                                    {{latitude}}
                                    {{longitude}}
                                </span>
                                {{address}}',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		);

	}

	/**
	 * Get the 'local business' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.1.0
	 */
	private function get_local_business_schema() {

		return array(
			'label'              => 'LocalBusiness',
			'description'        => 'A local business.',
			'parents'            => array( 'place', 'organization' ),
			'css'                => 'wl-local-business',
			'uri'                => 'http://schema.org/LocalBusiness',
			'same_as'            => array(
				'http://rdf.freebase.com/ns/business/business_location',
				'https://schema.org/Store'
			),
			'custom_fields'      => array(),
			'microdata_template' => '',
			'templates'          => array(
				'subtitle' => '{{id}}'
			)
		);

	}

}
