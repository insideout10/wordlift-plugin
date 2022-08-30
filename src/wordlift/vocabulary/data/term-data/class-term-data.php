<?php
/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This interface defines a structure for term data provided via endpoint
 */
namespace Wordlift\Vocabulary\Data\Term_Data;

interface Term_Data {

	/**
	 * Should return an array which can be used by ui components.
	 *
	 * @return array
	 */
	public function get_data();

}
