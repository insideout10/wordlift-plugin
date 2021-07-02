<?php
/**
 * @since 3.32.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This is a interface for a Link for object interfaces.
 */

namespace Wordlift\Link;

use Wordlift_Schema_Service;

class Link_Builder {

	private $id;
	private $type;
	private $label;
	private $href;
	private $entity_url;
	/**
	 * @var Object_Link_Provider
	 */
	private $object_link_provider;

	public function __construct( $entity_url, $id ) {
		$this->entity_url = $entity_url;
		$this->id = $id;
		$this->object_link_provider = Object_Link_Provider::get_instance();
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
	 * @param $post_id
	 *
	 * @return string
	 */
	private function get_attributes_for_link( $post_id ) {
		/**
		 * Allow 3rd parties to add additional attributes to the anchor link.
		 *
		 * @since 3.26.0
		 */
		$default_attributes = array(
			'id' => implode( ';', $this->object_link_provider->get_same_as_uris( $this->id, $this->type ) )
		);
		$attributes         = apply_filters( 'wl_anchor_data_attributes', $default_attributes, $post_id );
		$attributes_html    = '';
		foreach ( $attributes as $key => $value ) {
			$attributes_html .= ' data-' . esc_html( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return $attributes_html;
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