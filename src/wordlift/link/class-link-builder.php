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
	/**
	 * @var Object_Link_Provider
	 */
	private $object_link_provider;

	public function __construct( $entity_url, $id ) {
		$this->entity_url           = $entity_url;
		$this->id                   = $id;
		$this->object_link_provider = Object_Link_Provider::get_instance();
		$this->type                 = $this->object_link_provider->get_object_type( $entity_url );
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

	private function get_attributes_for_link() {
		/**
		 * Allow 3rd parties to add additional attributes to the anchor link.
		 *
		 * @since 3.26.0
		 */
		$default_attributes = array(
			'id' => implode( ';', $this->object_link_provider->get_same_as_uris( $this->id, $this->type ) ),
		);

		/**
		 * @since 3.32.0
		 * Additional parameter {@link $this->type} is added to the filter denoting the type of
		 * the entity url by the enum values {@link Object_Type_Enum}
		 */
		$attributes      = apply_filters( 'wl_anchor_data_attributes', $default_attributes, $this->id, $this->type );
		$attributes_html = '';
		foreach ( $attributes as $key => $value ) {
			$attributes_html .= ' data-' . esc_html( $key ) . '="' . esc_attr( $value ) . '" ';
		}

		return $attributes_html;
	}

	/**
	 * Get a `title` attribute with an alternative label for the link.
	 *
	 * If an alternative title isn't available an empty string is returned.
	 *
	 * @return string A `title` attribute with an alternative label or an empty
	 *                string if none available.
	 * @since 3.32.0
	 */
	private function get_title_attribute() {

		// Get an alternative title.
		$title = $this->object_link_provider->get_link_title( $this->id, $this->label, $this->type );
		if ( ! empty( $title ) ) {
			return 'title="' . esc_attr( $title ) . '"';
		}

		return '';
	}

	/**
	 * @return string
	 */
	public function generate_link() {
		// Get an alternative title attribute.
		$title_attribute = $this->get_title_attribute();
		$attributes_html = $this->get_attributes_for_link();

		// Return the link.
		return "<a class=\"wl-entity-page-link\" $title_attribute href=\"{$this->href}\"$attributes_html>{$this->label}</a>";
	}

}
