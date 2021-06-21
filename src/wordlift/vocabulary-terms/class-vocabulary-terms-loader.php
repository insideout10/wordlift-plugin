<?php

namespace Wordlift\Vocabulary_Terms;

use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Vocabulary_Terms\Jsonld\Jsonld_Generator;


class Vocabulary_Terms_Loader extends Default_Loader {
	/**
	 * @var \Wordlift_Entity_Type_Service
	 */
	private $entity_type_service;


	/**
	 * Vocabulary_Terms_Loader constructor.
	 *
	 * @param $entity_type_service \Wordlift_Entity_Type_Service
	 */
	public function __construct( $entity_type_service ) {
		parent::__construct();
		$this->entity_type_service = $entity_type_service;

	}


	public function init_all_dependencies() {
		new Entity_Type();
		new Term_Metabox();
		$jsonld = new Jsonld_Generator( $this->entity_type_service );
		$jsonld->init();
	}

	protected function get_feature_slug() {
		return 'no-vocabulary-terms';
	}

	protected function get_feature_default_value() {
		return false;
	}


}