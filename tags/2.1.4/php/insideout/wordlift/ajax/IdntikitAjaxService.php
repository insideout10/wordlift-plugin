<?php

class WordLift_IdntikitAjaxService {

	public function redirectTo( $url ) {

		$siteKey = get_option( "wordlift_site_key", "" );

		header( "Location: $url" . ( false === strrpos( $url, "?" ) ? "?" : "&" ) . "wsk=$siteKey" );
		exit;
	}

}

?>