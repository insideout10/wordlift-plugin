<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This is a interface for a Link for object interfaces.
 */

namespace Wordlift\Link;

abstract class Link {

	public function __construct() {
		add_filter( 'wl_object_link_providers', array( $this, 'register'));
	}

	public function register( $providers ) {
		$providers[] = $this;
		return $providers;
	}

	abstract  public function get_link_title( $id, $label );

	abstract  public function get_same_as_uris( $id );

}