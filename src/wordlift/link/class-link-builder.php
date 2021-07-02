<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This is a interface for a Link for object interfaces.
 */

namespace Wordlift\Link;

class Link_Builder {

	private $id;
	private $type;
	private $label;
	private $href;
	private $entity_url;

	public function __construct( $entity_url, $id ) {
		$this->entity_url = $entity_url;
		$this->id = $id;
	}

	public static function create( $entity_url, $id ) {
		return new Link_Builder( $entity_url, $id );
	}

	public function label( $label ) {
		$this->label = $label;

		return $this;
	}

	public function href( $href ) {
		$this->href = $href;

		return $this;
	}

	/**
	 * @return string
	 */
	public function generate_link() {
		// Get an alternative title attribute.
		$title_attribute = $this->get_title_attribute( $post_id, $label );
		$attributes_html = $this->get_attributes_for_link( $post_id );

		// Return the link.
		return "<a class='wl-entity-page-link' $title_attribute href='$href' $attributes_html>$label</a>";
	}


}