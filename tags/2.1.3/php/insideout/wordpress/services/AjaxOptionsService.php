<?php

class WordPress_AjaxOptionsService {

	public function getOption( $name, $default = "" ) {
		echo get_option( $name, $default );

		return WordPress_AjaxProxy::CALLBACK_RETURN_NULL;
	}

	public function setOption( $name, $value ) {

		if ( get_option( $name ) != $value ) {
			return update_option( $name, $value );
		}

		$deprecated = " ";
		$autoload = "no";
		add_option( $name, $value, $deprecated, $autoload );

		return WordPress_AjaxProxy::CALLBACK_RETURN_NULL;
	}

}

?>