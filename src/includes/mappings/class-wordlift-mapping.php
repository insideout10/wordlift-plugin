<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 01/08/2017
 * Time: 17:47
 */

class Wordlift_Mapping {
	private $source;
	private $destination;


	/**
	 * Wordlift_Mapping constructor.
	 *
	 * @param $source
	 * @param $destination
	 */
	public function __construct( $source, $destination ) {

		$this->source      = $source;
		$this->destination = $destination;

	}

	/**
	 * @return mixed
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * @return mixed
	 */
	public function get_destination() {
		return $this->destination;
	}


}