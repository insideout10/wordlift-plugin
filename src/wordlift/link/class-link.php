<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This is a interface for a Link for object interfaces.
 */

namespace Wordlift\Link;

interface Link {

	public function get_link_title( $id, $label_to_be_ignored );

	public function get_same_as_uris( $id );

	public function get_id( $uri );

	public function get_synonyms( $id );

	public function get_permalink( $id );

	/**
	 * Return the edit page link.
	 *
	 * @param $id
	 *
	 * @return string
	 */
	public function get_edit_page_link( $id );
}
