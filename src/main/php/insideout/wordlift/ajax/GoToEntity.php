<?php

class WordLift_GoToEntity {

	public $pageId = 45;

	public function redirectToEntity( $e ) {

		$htmlEntityLink = get_page_link( $this->pageId );
		$htmlEntityLink .= "&e=" . urlencode( $e );

		header( "Location: $htmlEntityLink" );
		end();

	}

}

?>