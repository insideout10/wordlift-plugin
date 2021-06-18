<?php
/**
 * @since 3.31.6
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
namespace Wordlift\Metabox\Field;

interface Field {

	public function get_data();

	public function save_data( $values );

}