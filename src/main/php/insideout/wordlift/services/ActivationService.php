<?php

class WordLift_ActivationService {

	public $logger;
	public $apiUrl;
	public $menuUrl;

	public function activate() {

		$this->logger->trace( "Activating the WordLift Plugin..." );

		$data = array(
			"url" => $this->getUrl()
		);

		// use key 'http' even if you send the request to https://...
		$options = array('http' =>
						array(
							'method'  => 'POST',
							'content' => json_encode( $data ),
							'header'=>  "Content-Type: application/json\r\n" .
										"Accept: application/json\r\n"
						)
					);
		$context  = stream_context_create($options);
		$result = file_get_contents( $this->apiUrl, false, $context );

		$json = json_decode( $result );

		if ( property_exists( $json, "key") )
			$siteKey = $json->key;
		else
			$siteKey = $this->getSiteKey();			

		if ( NULL != $siteKey )
			add_option( "wordlift_site_key", $siteKey );

		// create a phantom page for the entity page.
		$this->createPage();
	}

	private function getUrl() {
		return admin_url( $this->menuUrl );
	}

	private function getSiteKey() {

		$url = $this->apiUrl . "?url=" . urlencode( $this->getUrl() );

		// use key 'http' even if you send the request to https://...
		$options = array('http' =>
						array(
							'method'  => 'GET',
							'header'=>  "Content-Type: application/json\r\n" .
										"Accept: application/json\r\n"
						)
					);
		$context  = stream_context_create($options);
		$result = file_get_contents( $url, false, $context );

		$json = json_decode( $result );

		if ( property_exists( $json, "key") )
			return $json->key;
		else
			return NULL;
	}

	private function createPage() {

		$entityPage = array(
				"post_type" => "page",
				"post_status" => "publish",
				"post_name" => "entity",
				"post_content" => "[wordlift.entity]"
			);

		$error = NULL;
		wp_insert_post( $post, $error );

		$this->logger->info( var_dump($error) );

	}

}

?>