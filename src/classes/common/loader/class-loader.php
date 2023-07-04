<?php
/**
 * The loader classes are responsible for initializing and adding feature to registry.
 *
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift\Common\Loader;

interface Loader {

	/**
	 * The loader should register the feature to feature registry.
	 *
	 * @return void
	 */
	public function init_feature();

}
