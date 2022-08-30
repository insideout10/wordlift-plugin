<?php
/**
 * This abstract class loads the feature to registry.
 *
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Common\Loader;

use Wordlift\Features\Features_Registry;

abstract class Default_Loader implements Loader {
	/**
	 * @var Features_Registry
	 */
	private $features_registry;

	/**
	 * Default_Loader constructor.
	 */
	public function __construct() {
		$this->features_registry = Features_Registry::get_instance();
	}

	/**
	 * Initialize all the dependencies needed by the feature inside this method.
	 *
	 * @return void
	 */
	abstract public function init_all_dependencies();

	abstract protected function get_feature_slug();

	/**
	 * @return bool true if the feature wants to be enabled by default
	 */
	abstract protected function get_feature_default_value();

	/**
	 * Register feature to registry.
	 */
	public function init_feature() {
		$this->features_registry->register_feature_from_slug(
			$this->get_feature_slug(),
			$this->get_feature_default_value(),
			array( $this, 'init_all_dependencies' )
		);

	}

}
