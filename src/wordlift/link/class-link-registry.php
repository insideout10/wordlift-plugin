<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This class provides a abstract layer for content filter service to generate a link by entity uri service.
 */
namespace Wordlift\Link;

class Link_Registry {

	private  $link_providers = array();

	public function __construct() {
		add_action('plugins_loaded', array( $this, 'init_registry'));
	}

	public function init_registry() {
		$this->link_providers = apply_filters( 'wl_object_link_providers', array() );
	}
	public function get_link() {

	}



}