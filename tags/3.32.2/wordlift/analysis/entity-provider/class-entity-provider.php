<?php

namespace Wordlift\Analysis\Entity_Provider;

abstract class Entity_Provider {

	public function __construct() {
		add_filter( 'wl_analysis_entity_providers', array( $this, 'register'));
	}

	public function register( $providers ) {
		$providers[] = $this;
		return $providers;
	}
	abstract public function get_entity( $uri );
}