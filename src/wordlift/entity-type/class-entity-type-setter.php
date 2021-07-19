<?php
/**
 * @since ?.??.??
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Entity_Type;

class Entity_Type_Setter {


	public static function get_starter_features() {

		return array(





		);

	}





	public function __construct() {
		add_action( 'wl_package_type_changed', array( $this, 'wl_package_type_changed' ) );
	}

	public function wl_package_type_changed() {

	}


}