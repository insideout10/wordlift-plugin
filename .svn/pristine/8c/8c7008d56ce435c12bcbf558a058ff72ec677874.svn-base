<?php

class WordLift_UserRegistrationAjaxService {

	public $apiUrl;

	public function register( $requestBody ) {

		$siteKey = get_option( 'wordlift_site_key' );

		$data = array(
			"userName" => $userName,
			"email" => $email,
			"password" => $password,
			"confirmPassword" => $confirmPassword
		);

		$options = array(
			"http" => array(
				"method"  => "POST",
				"content" => $requestBody,
				"header" =>  "Content-Type: application/json\r\n" .
							"Accept: application/json\r\n" .
							"Site-Key: $siteKey"
			)
		);

		$context  = stream_context_create( $options );
		$result = file_get_contents( $this->apiUrl, false, $context );
		$response = json_decode( $result );

		return $response;

	}

}

?>