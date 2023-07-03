<?php
namespace Wordlift\Vocabulary\Data\Term_Count;

/**
 * This is the interface for getting term count.
 *
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

interface Term_Count {

	/**
	 * Return the term count which needs to be processed by the editor.
	 *
	 * @return int
	 */
	public function get_term_count();

}

