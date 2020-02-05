<?php
/**
 * This file helps to register the meta boxes in the edit/add post screen.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */

namespace Wordlift\FAQ;

class FAQ_Metabox {

	const FAQ_METABOX_ID = 'wl-faq-meta-box';

	public function __construct() {
		$this->register_faq_metabox();
	}

	/**
	 * Returns or does nothing, the meta box function invokes this
	 * callback, if any thing needed to be added inside FAQ meta box
	 * then it should be echoed inside this function.
	 */
	public function echo_meta_box_template() {
	}

	/**
	 * Registers the FAQ meta box.
	 */
	public function register_faq_metabox() {
		add_meta_box(
			self::FAQ_METABOX_ID,
			__( 'Wordlift FAQ', 'wordlift' ),
			array($this, 'echo_meta_box_template'),
			'post'
		);
	}

}