<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Vocabulary_Terms\Hooks\Term_Save;
use Wordlift\Vocabulary_Terms\Jsonld\Jsonld_Generator;
use Wordlift\Vocabulary_Terms\Jsonld\Post_Jsonld;

class Vocabulary_Terms_Loader extends Default_Loader {
	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;
	/**
	 * @var \Wordlift_Property_Getter
	 */
	private $property_getter;

	/**
	 * Vocabulary_Terms_Loader constructor.
	 *
	 * @param $entity_type_service \Wordlift_Entity_Type_Service
	 * @param \Wordlift_Property_Getter                         $property_getter
	 */
	public function __construct( $entity_type_service, $property_getter ) {
		parent::__construct();
		$this->entity_type_service = $entity_type_service;
		$this->property_getter     = $property_getter;
	}

	public function init_all_dependencies() {
		new Entity_Type();
		new Term_Metabox();
		$jsonld = new Jsonld_Generator( $this->entity_type_service, $this->property_getter );
		$jsonld->init();
		$term_save_hook = new Term_Save();
		$term_save_hook->init();
		$post_jsonld = new Post_Jsonld();
		$post_jsonld->init();
	}

	protected function get_feature_slug() {
		return 'no-vocabulary-terms';
	}

	protected function get_feature_default_value() {
		return false;
	}

}
