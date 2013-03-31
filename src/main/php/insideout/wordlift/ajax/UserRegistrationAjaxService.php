<?php

class WordLift_UserRegistrationAjaxService
{

    public $apiUrl;

    public function register($requestBody)
    {
        $siteKey = get_option("wordlift_site_key");

        $operations = WordLift_HttpOperations::create(
            $this->apiUrl,
            WordLift_HttpOperations::CONTENT_TYPE_JSON,
            WordLift_HttpOperations::CONTENT_TYPE_JSON,
            array("Site-Key" => $siteKey)
        );

        $response = $operations->post(
            null,
            $requestBody
        );

        return json_decode($response["body"]);

        // $options = array(
        //     "http" => array(
        //         "method"  => "POST",
        //         "content" => $requestBody,
        //         "header" =>  "Content-Type: application/json\r\n" .
        //                     "Accept: application/json\r\n" .
        //                     "Site-Key: $siteKey"
        //     )
        // );

        // $context  = stream_context_create($options);
        // $result = file_get_contents($this->apiUrl, false, $context);
        // $response = json_decode($result);

        // return $response;
    }
}

?>