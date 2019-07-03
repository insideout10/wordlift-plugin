<?php
/**
 * Services: WordLift Schema Service.
 *
 * This file defines the Wordlift_Schema_Service class.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Provides constants and methods related to WordLift's schema.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
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
	 * The 'recipeCuisine' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_RECIPE_CUISINE = 'wl_schema_recipe_cuisine';

	/**
	 * The 'recipeIngredient' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_RECIPE_INGREDIENT = 'wl_schema_recipe_ingredient';

	/**
	 * The 'calories' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_NUTRITION_INFO_CALORIES = 'wl_schema_nutrition_information_calories';

	/**
	 * The 'recipeInstructions' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_RECIPE_INSTRUCTIONS = 'wl_schema_recipe_instructions';

	/**
	 * The 'recipeYield' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_RECIPE_YIELD = 'wl_schema_recipe_yield';

	/**
	 * The 'prepTime' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_PREP_TIME = 'wl_schema_prep_time';

	/**
	 * The 'cookTime' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_COOK_TIME = 'wl_schema_cook_time';

	/**
	 * The 'totalTime' field name.
	 *
	 * @since 3.14.0
	 */
	const FIELD_TOTAL_TIME = 'wl_schema_total_time';

	/**
	 * The 'performer' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_PERFORMER = 'wl_schema_performer';

	/**
	 * The 'offers' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_OFFERS = 'wl_schema_offers';

	/**
	 * The 'availablity' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_AVAILABILITY = 'wl_schema_availability';

	/**
	 * The 'inventoryLevel' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_INVENTORY_LEVEL = 'wl_schema_inventory_level';

	/**
	 * The 'price' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_PRICE = 'wl_schema_price';

	/**
	 * The 'priceCurrency' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_PRICE_CURRENCY = 'wl_schema_price_currency';

	/**
	 * The 'availabilityStarts' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_AVAILABILITY_STARTS = 'wl_schema_availability_starts';

	/**
	 * The 'availabilityEnds' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_AVAILABILITY_ENDS = 'wl_schema_availability_ends';

	/**
	 * The 'validFrom' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_VALID_FROM = 'wl_schema_valid_from';

	/**
	 * The 'priceValidUntil' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_PRICE_VALID_UNTIL = 'wl_schema_valid_until';

	/**
	 * The 'itemOffered' field name.
	 *
	 * @since 3.18.0
	 */
	const FIELD_ITEM_OFFERED = 'wl_schema_item_offered';

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
	 * The 'dateTime' data type name.
	 *
	 * @since 3.15.0
	 */
	const DATA_TYPE_DATE_TIME = 'dateTime';

	/**
	 * The 'time' data type name.
	 *
	 * @since 3.14.0
	 */
	const DATA_TYPE_DURATION = 'duration';

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
	 * The multiline text data type name.
	 *
	 * @since 3.14.0
	 */
	const DATA_TYPE_MULTILINE = 'multiline';

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
	 * The schema.org Offer type URI.
	 *
	 * @since 3.18.0
	 */
	const SCHEMA_OFFER_TYPE = 'http://schema.org/Offer';

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
	 * @var \Wordlift_Log_Service $log The Log service.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Post_Property_Storage_Factory} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Storage_Factory $storage_factory The {@link Wordlift_Post_Property_Storage_Factory} instance.
	 */
	private $storage_factory;

	/**
	 * The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 */
	private $rendition_factory;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * The web site configured language code.
	 *
	 * @since  3.15.0
	 * @access private
	 * @var string $language_code The web site configured language code.
	 */
	private $language_code;

	/**
	 * Wordlift_Schema_Service constructor.
	 *
	 * @since 3.1.0
	 *
	 * @param \Wordlift_Storage_Factory                $storage_factory The {@link Wordlift_Post_Property_Storage_Factory} instance.
	 * @param \Wordlift_Sparql_Tuple_Rendition_Factory $rendition_factory The {@link Wordlift_Sparql_Tuple_Rendition_Factory} instance.
	 * @param \Wordlift_Configuration_Service          $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $storage_factory, $rendition_factory, $configuration_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Schema_Service' );

		$this->storage_factory       = $storage_factory;
		$this->rendition_factory     = $rendition_factory;
		$this->configuration_service = $configuration_service;
		$this->language_code         = $this->configuration_service->get_language_code();

		$schemas = array(
			'article'        => $this->get_article_schema(),
			'thing'          => $this->get_thing_schema(),
			'creative-work'  => $this->get_creative_work_schema(),
			'event'          => $this->get_event_schema(),
			'organization'   => $this->get_organization_schema(),
			'person'         => $this->get_person_schema(),
			'place'          => $this->get_place_schema(),
			'local-business' => $this->get_local_business_schema(),
			'recipe'         => $this->get_recipe_schema(),
			'web-page'       => $this->get_web_page_schema(),
			'offer'          => $this->get_offer_schema(),
		);

		// Set the taxonomy data.
		// Note: parent types must be defined before child types.
		/**
		 * Alter the configured schemas.
		 *
		 * Enable 3rd parties to alter WordLift's schemas array.
		 *
		 * @since  3.19.1
		 *
		 * @param    array $schemas The array of schemas.
		 */
		$this->schema = apply_filters( 'wl_schemas', $schemas );

		// Create a singleton instance of the Schema service, useful to provide static functions to global functions.
		self::$instance = $this;

		// Hook the `init` to allow plugins to add their schemas.
		add_action( 'init', array( $this, 'init' ) );

	}

	/**
	 * Hook to the `init`, allow late binding plugins to add their schema.
	 *
	 * @since 3.19.2
	 */
	public function init() {

		$this->schema = apply_filters( 'wl_schemas_init', $this->schema );

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
	 * Get all renditions for each WordLift's schema.
	 *
	 * @since 3.18.0
	 *
	 * @return array An array with the schema renditions.
	 */
	public function get_renditions() {
		// Get the custom fields.
		$renditions = array_reduce(
			$this->schema,
			function ( $carry, $item ) {
				return array_merge( $carry, $item['linked_data'] );
			},
			array()
		);

		// Return the schemas.
		return $renditions;
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
			'linked_data'   => array(
				// ### Title to rdfs:label.
				$this->rendition_factory->create(
					$this->storage_factory->post_title(),
					Wordlift_Query_Builder::RDFS_LABEL_URI,
					null,
					$this->language_code
				),
				// ### Title to dct:title.
				$this->rendition_factory->create(
					$this->storage_factory->post_title(),
					'http://purl.org/dc/terms/title',
					null,
					$this->language_code
				),
				// ### Alternative title to rdfs:label.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY ),
					Wordlift_Query_Builder::RDFS_LABEL_URI,
					null,
					$this->language_code
				),
				// ### Alternative title to dct:title.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY ),
					'http://purl.org/dc/terms/title',
					null,
					$this->language_code
				),
				// ### Title to schema:name.
				$this->rendition_factory->create(
					$this->storage_factory->post_title(),
					'http://schema.org/name',
					null,
					$this->language_code
				),
				// ### Alternative title to schema:alterName.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( Wordlift_Entity_Service::ALTERNATIVE_LABEL_META_KEY ),
					'http://schema.org/alternateName',
					null,
					$this->language_code
				),
				// ### schema:url.
				$this->rendition_factory->create(
					$this->storage_factory->url_property(),
					Wordlift_Query_Builder::SCHEMA_URL_URI,
					self::DATA_TYPE_URI
				),
				// ### schema:description.
				$this->rendition_factory->create(
					$this->storage_factory->post_description_no_tags_no_shortcodes(),
					'http://schema.org/description',
					null,
					$this->language_code
				),
				// ### owl:sameAs.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_SAME_AS ),
					'http://www.w3.org/2002/07/owl#sameAs',
					self::DATA_TYPE_URI
				),
				// ### schema:sameAs.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_SAME_AS ),
					'http://schema.org/sameAs',
					self::DATA_TYPE_URI
				),
				// ### rdf:type.
				$this->rendition_factory->create(
					$this->storage_factory->schema_class(),
					Wordlift_Query_Builder::RDFS_TYPE_URI,
					self::DATA_TYPE_URI
				),
				// ### schema:image.
				$this->rendition_factory->create(
					$this->storage_factory->post_images(),
					Wordlift_Query_Builder::SCHEMA_IMAGE_URI,
					self::DATA_TYPE_URI
				),
				// ### dct:relation.
				$this->rendition_factory->create(
					$this->storage_factory->relations(),
					Wordlift_Query_Builder::DCTERMS_RELATION_URI,
					self::DATA_TYPE_URI
				),
			),
		);

	}

	/**
	 * Get the 'web-page' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.18.0
	 */
	private function get_web_page_schema() {

		return array(
			'css_class'   => 'wl-webpage',
			'uri'         => 'http://schema.org/WebPage',
			'linked_data' => array(
				// ### schema:headline.
				$this->rendition_factory->create(
					$this->storage_factory->post_title(),
					'http://schema.org/headline',
					null,
					$this->language_code
				),
				// ### schema:url.
				$this->rendition_factory->create(
					$this->storage_factory->url_property(),
					Wordlift_Query_Builder::SCHEMA_URL_URI,
					self::DATA_TYPE_URI
				),
				// ### rdf:type.
				$this->rendition_factory->create(
					$this->storage_factory->schema_class(),
					Wordlift_Query_Builder::RDFS_TYPE_URI,
					self::DATA_TYPE_URI
				),
				// ### dcterms:references.
				$this->rendition_factory->create(
					$this->storage_factory->relations(),
					Wordlift_Query_Builder::DCTERMS_REFERENCES_URI,
					self::DATA_TYPE_URI,
					$this->language_code
				),
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
			'linked_data'   => array(
				// ### schema:author.
				$this->rendition_factory->create(
					$this->storage_factory->author_uri(),
					Wordlift_Query_Builder::SCHEMA_AUTHOR_URI,
					self::DATA_TYPE_URI
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$parent_schema           = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $parent_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $parent_schema['linked_data'] );

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
					'export_type' => 'xsd:dateTime',
					'constraints' => '',
				),
				self::FIELD_DATE_END   => array(
					'predicate'   => 'http://schema.org/endDate',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:dateTime',
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
				self::FIELD_PERFORMER  => array(
					'predicate'   => 'http://schema.org/performer',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Person',
					'constraints' => array(
						'uri_type'    => array( 'Person', 'Organization' ),
						'cardinality' => INF,
					),
				),
				self::FIELD_OFFERS     => array(
					'predicate'   => 'http://schema.org/offers',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Offer',
					'constraints' => array(
						'uri_type'    => array( 'Offer' ),
						'cardinality' => INF,
					),
				),
			),
			'linked_data'   => array(
				// ### schema:startDate.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_DATE_START ),
					'http://schema.org/startDate',
					self::DATA_TYPE_DATE_TIME
				),
				// ### schema:endDate.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_DATE_END ),
					'http://schema.org/endDate',
					self::DATA_TYPE_DATE_TIME
				),
				// ### schema:location.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_LOCATION ),
					'http://schema.org/location',
					self::DATA_TYPE_URI
				),
				// ### schema:performer.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_PERFORMER ),
					'http://schema.org/performer',
					self::DATA_TYPE_URI
				),
				// ### schema:offers.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_OFFERS ),
					'http://schema.org/offers',
					self::DATA_TYPE_URI
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$parent_schema           = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $parent_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $parent_schema['linked_data'] );

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
			'linked_data'   => array(
				// ### schema:legalName.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_LEGAL_NAME ),
					'http://schema.org/legalName'
				),
				// ### schema:founder.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_FOUNDER ),
					'http://schema.org/founder',
					self::DATA_TYPE_URI
				),
				// ### schema:email.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_EMAIL ),
					'http://schema.org/email'
				),
				// ### schema:telephone.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_TELEPHONE ),
					'http://schema.org/telephone'
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$parent_schema           = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $parent_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $parent_schema['linked_data'] );

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
			'linked_data'   => array(
				// ### schema:knows.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_KNOWS ),
					'http://schema.org/knows',
					self::DATA_TYPE_URI
				),
				// ### schema:birthDate.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_BIRTH_DATE ),
					'http://schema.org/birthDate',
					self::DATA_TYPE_DATE
				),
				// ### schema:birthPlace.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_BIRTH_PLACE ),
					'http://schema.org/birthPlace',
					self::DATA_TYPE_URI
				),
				// ### schema:affiliation.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_AFFILIATION ),
					'http://schema.org/affiliation',
					self::DATA_TYPE_URI
				),
				// ### schema:email.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_EMAIL ),
					'http://schema.org/email'
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$parent_schema           = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $parent_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $parent_schema['linked_data'] );

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
			'linked_data'   => array(
				$this->rendition_factory->create_address(
					$this->storage_factory,
					$this->language_code
				),
			),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$parent_schema           = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $parent_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $parent_schema['linked_data'] );

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
			'linked_data'   => array(),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
		);

		// Merge the custom fields with those provided by the place and organization schema.
		$place_schema            = $this->get_place_schema();
		$organization_schema     = $this->get_organization_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $place_schema['custom_fields'], $organization_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $place_schema['linked_data'], $organization_schema['linked_data'] );

		return $schema;
	}

	/**
	 * Get the 'recipe' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.14.0
	 */
	private function get_recipe_schema() {

		$schema = array(
			'label'         => 'Recipe',
			'description'   => 'A Recipe.',
			'parents'       => array( 'CreativeWork' ),
			'css_class'     => 'wl-recipe',
			'uri'           => 'http://schema.org/Recipe',
			'same_as'       => array(),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
			'custom_fields' => array(
				self::FIELD_RECIPE_CUISINE          => array(
					'predicate'   => 'http://schema.org/recipeCuisine',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					'metabox'     => array(
						'label' => __( 'Recipe cuisine', 'wordlift' ),
					),
				),
				self::FIELD_RECIPE_INGREDIENT       => array(
					'predicate'   => 'http://schema.org/recipeIngredient',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => array(
						'cardinality' => INF,
					),
					'metabox'     => array(
						'label' => __( 'Recipe ingredient', 'wordlift' ),
					),
				),
				self::FIELD_RECIPE_INSTRUCTIONS     => array(
					'predicate'   => 'http://schema.org/recipeInstructions',
					'type'        => self::DATA_TYPE_MULTILINE,
					'export_type' => 'xsd:string',
					'constraints' => '',
					'metabox'     => array(
						'class' => 'Wordlift_Metabox_Field_Multiline',
						'label' => __( 'Recipe instructions', 'wordlift' ),
					),
				),
				self::FIELD_RECIPE_YIELD            => array(
					'predicate'   => 'http://schema.org/recipeYield',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					'metabox'     => array(
						'label' => __( 'Recipe number of servings', 'wordlift' ),
					),
				),
				self::FIELD_RECIPE_INGREDIENT       => array(
					'predicate'   => 'http://schema.org/recipeIngredient',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => array(
						'cardinality' => INF,
					),
					'metabox'     => array(
						'label' => __( 'Recipe ingredient', 'wordlift' ),
					),
				),
				self::FIELD_NUTRITION_INFO_CALORIES => array(
					'predicate'   => 'http://schema.org/calories',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'constraints' => '',
					'metabox'     => array(
						'label' => __( 'Calories (e.g. 240 calories)', 'wordlift' ),
					),
				),
				self::FIELD_PREP_TIME               => array(
					'predicate'   => 'http://schema.org/prepTime',
					'type'        => self::DATA_TYPE_DURATION,
					'export_type' => 'xsd:time',
					'constraints' => '',
					'metabox'     => array(
						'class' => 'Wordlift_Metabox_Field_Duration',
						'label' => __( 'Recipe preparation time (e.g. 1:30)', 'wordlift' ),
					),
				),
				self::FIELD_COOK_TIME               => array(
					'predicate'   => 'http://schema.org/cookTime',
					'type'        => self::DATA_TYPE_DURATION,
					'export_type' => 'xsd:time',
					'constraints' => '',
					'metabox'     => array(
						'class' => 'Wordlift_Metabox_Field_Duration',
						'label' => __( 'Recipe cook time (e.g. 1:30)', 'wordlift' ),
					),
				),
				self::FIELD_TOTAL_TIME              => array(
					'predicate'   => 'http://schema.org/totalTime',
					'type'        => self::DATA_TYPE_DURATION,
					'export_type' => 'xsd:time',
					'constraints' => '',
					'metabox'     => array(
						'class' => 'Wordlift_Metabox_Field_Duration',
						'label' => __( 'Recipe total time (e.g. 1:30)', 'wordlift' ),
					),
				),
			),
			'linked_data'   => array(
				// ### schema:recipeCuisine.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_RECIPE_CUISINE ),
					'http://schema.org/recipeCuisine'
				),
				// ### schema:recipeIngredient.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_RECIPE_INGREDIENT ),
					'http://schema.org/recipeIngredient'
				),
				// ### schema:recipeInstructions.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_RECIPE_INSTRUCTIONS ),
					'http://schema.org/recipeInstructions'
				),
				// ### schema:recipeYield.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_RECIPE_YIELD ),
					'http://schema.org/recipeYield'
				),
				// ### schema:prepTime.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_PREP_TIME ),
					'http://schema.org/prepTime',
					self::DATA_TYPE_DURATION
				),
				// ### schema:cookTime.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_COOK_TIME ),
					'http://schema.org/cookTime',
					self::DATA_TYPE_DURATION
				),
				// ### schema:totalTime.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_TOTAL_TIME ),
					'http://schema.org/totalTime',
					self::DATA_TYPE_DURATION
				),
			),
		);

		// Merge the custom fields with those provided by the parent schema.
		$parent_schema           = $this->get_creative_work_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $parent_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $parent_schema['linked_data'] );

		return $schema;
	}

	/**
	 * Get the 'offer' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.18.0
	 */
	private function get_offer_schema() {

		$schema = array(
			'label'         => 'Offer',
			'description'   => 'An offer. ',
			'parents'       => array( 'thing' ),
			'css_class'     => 'wl-offer',
			'uri'           => self::SCHEMA_OFFER_TYPE,
			'same_as'       => array(),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
			'custom_fields' => array(
				self::FIELD_AVAILABILITY        => array(
					'predicate'   => 'http://schema.org/availability',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
					'metabox'     => array(
						'class' => 'Wordlift_Metabox_Field_Select',
					),
					'options'     => array(
						'Discontinued'        => esc_html__( 'Discontinued', 'wordlift' ),
						'InStock'             => esc_html__( 'In Stock', 'wordlift' ),
						'InStoreOnly'         => esc_html__( 'In Store Only', 'wordlift' ),
						'LimitedAvailability' => esc_html__( 'Limited Availability', 'wordlift' ),
						'OnlineOnly'          => esc_html__( 'Online Only', 'wordlift' ),
						'OutOfStock'          => esc_html__( 'Out of Stock', 'wordlift' ),
						'PreOrder'            => esc_html__( 'Pre Order', 'wordlift' ),
						'PreSale'             => esc_html__( 'Pre Sale', 'wordlift' ),
						'SoldOut'             => esc_html__( 'Sold Out', 'wordlift' ),
					),
				),
				self::FIELD_PRICE               => array(
					'predicate'   => 'http://schema.org/price',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:integer',
					'metabox'     => array(
						'class' => 'Wordlift_Metabox_Field_Integer',
					),
				),
				self::FIELD_PRICE_CURRENCY      => array(
					'predicate'   => 'http://schema.org/priceCurrency',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:string',
				),
				self::FIELD_AVAILABILITY_STARTS => array(
					'predicate'   => 'http://schema.org/availabilityStarts',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:dateTime',
				),
				self::FIELD_AVAILABILITY_ENDS   => array(
					'predicate'   => 'http://schema.org/availabilityEnds',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:dateTime',
				),
				self::FIELD_INVENTORY_LEVEL     => array(
					'predicate'   => 'http://schema.org/inventoryLevel',
					'type'        => self::DATA_TYPE_STRING,
					'export_type' => 'xsd:integer',
					'metabox'     => array(
						'class' => 'Wordlift_Metabox_Field_Integer',
					),
				),
				self::FIELD_VALID_FROM          => array(
					'predicate'   => 'http://schema.org/validFrom',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:dateTime',
				),
				self::FIELD_PRICE_VALID_UNTIL   => array(
					'predicate'   => 'http://schema.org/priceValidUntil',
					'type'        => self::DATA_TYPE_DATE,
					'export_type' => 'xsd:dateTime',
				),
				self::FIELD_ITEM_OFFERED        => array(
					'predicate'   => 'http://schema.org/itemOffered',
					'type'        => self::DATA_TYPE_URI,
					'export_type' => 'http://schema.org/Thing',
					'constraints' => array(
						'uri_type'    => array(
							'Event',
							'Thing',
						),
						'cardinality' => INF,
					),
				),
			),
			'linked_data'   => array(
				// ### schema:availability.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_AVAILABILITY ),
					'http://schema.org/availability',
					null
				),
				// ### schema:availabilityStarts.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_AVAILABILITY_STARTS ),
					'http://schema.org/availabilityStarts',
					self::DATA_TYPE_DATE_TIME
				),
				// ### schema:availabilityEnds.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_AVAILABILITY_ENDS ),
					'http://schema.org/availabilityEnds',
					self::DATA_TYPE_DATE_TIME
				),
				// ### schema:inventoryLevel.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_INVENTORY_LEVEL ),
					'http://schema.org/inventoryLevel',
					self::DATA_TYPE_INTEGER
				),
				// ### schema:price.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_PRICE ),
					'http://schema.org/price',
					self::DATA_TYPE_INTEGER
				),
				// ### schema:priceCurrency.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_PRICE_CURRENCY ),
					'http://schema.org/priceCurrency',
					null
				),
				// ### schema:validFrom.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_VALID_FROM ),
					'http://schema.org/validFrom',
					null
				),
				// ### schema:priceValidUntil.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta( self::FIELD_PRICE_VALID_UNTIL ),
					'http://schema.org/priceValidUntil',
					null
				),
				// ### schema:itemOffered.
				$this->rendition_factory->create(
					$this->storage_factory->post_meta_to_uri( self::FIELD_ITEM_OFFERED ),
					'http://schema.org/itemOffered',
					self::DATA_TYPE_URI
				),
			),
		);

		// Merge the custom fields with those provided by the thing schema.
		$parent_schema           = $this->get_thing_schema();
		$schema['custom_fields'] = array_merge( $schema['custom_fields'], $parent_schema['custom_fields'] );
		$schema['linked_data']   = array_merge( $schema['linked_data'], $parent_schema['linked_data'] );

		return $schema;
	}

	/**
	 * Get the 'article' schema.
	 *
	 * @return array An array with the schema configuration.
	 *
	 * @since 3.15.0
	 */
	private function get_article_schema() {

		$schema = array(
			'label'         => 'Article',
			'description'   => 'An Article.',
			'parents'       => array(),
			'css_class'     => 'wl-article',
			'uri'           => 'http://schema.org/Article',
			'same_as'       => array(),
			'templates'     => array(
				'subtitle' => '{{id}}',
			),
			'custom_fields' => array(),
			'linked_data'   => array(
				// ### schema:headline.
				$this->rendition_factory->create(
					$this->storage_factory->post_title(),
					'http://schema.org/headline',
					null,
					$this->language_code
				),
				// ### schema:url.
				$this->rendition_factory->create(
					$this->storage_factory->url_property(),
					Wordlift_Query_Builder::SCHEMA_URL_URI,
					self::DATA_TYPE_URI
				),
				// ### rdf:type.
				$this->rendition_factory->create(
					$this->storage_factory->schema_class(),
					Wordlift_Query_Builder::RDFS_TYPE_URI,
					self::DATA_TYPE_URI
				),
				// ### dcterms:references.
				$this->rendition_factory->create(
					$this->storage_factory->relations(),
					Wordlift_Query_Builder::DCTERMS_REFERENCES_URI,
					self::DATA_TYPE_URI,
					$this->language_code
				),
			),
		);

		return $schema;
	}

}
