<?php

/**
 * Preset the alternate name.
 *
 * @since 3.39.0
 */
class Wordlift_Install_3_39_1 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.39.1';

	public function install() {

		Wordlift_Configuration_Service::get_instance()->set_alternate_name(
			wp_strip_all_tags( get_bloginfo( 'description' ) )
		);

	}

}
