<?php

class WordLift_HttpOperations
{
    const CONTENT_TYPE_JSON = "application/json";

    const DEFAULT_TIMEOUT = 60;

    private $apiUrl;
    private $contentType;
    private $accept;
    private $timeout;

    public static function create(
        $apiUrl,
        $contentType,
        $accept,
        $timeout = self::DEFAULT_TIMEOUT
    ) {

        return new self(
            $apiUrl,
            $contentType,
            $accept,
            $timeout
        );
    }

    function __construct(
        $apiUrl,
        $contentType,
        $accept,
        $timeout = self::DEFAULT_TIMEOUT
    ) {
        $this->apiUrl = $apiUrl;
        $this->contentType = $contentType;
        $this->accept = $accept;
        $this->timeout = $timeout;
    }

    public function post($parameters, $body)
    {
        $url = $this->apiUrl . $this->getQueryString($parameters);

        $options = array(
            "timeout" => $this->timeout,
            "headers" => $this->getHeaders(),
            "body" => $body
        );

        $response = wp_remote_post($url, $options);

        // if (is_wp_error($response)) {
        //     $error_message = $response->get_error_message();
        //     echo "Something went wrong: $error_message";
        // } else {
        //     echo 'Response:<pre>';
        //     print_r($response);
        //     echo '</pre>';
        // }

        return $response;        
    }

    public function get($parameters)
    {
        $url = $this->apiUrl . $this->getQueryString($parameters);

        $options = array(
            "timeout" => $this->timeout,
            "headers" => $this->getHeaders()
        );

        $response = wp_remote_get($url, $options);

        // if (is_wp_error($response)) {
        //     $error_message = $response->get_error_message();
        //     echo "Something went wrong: $error_message";
        // } else {
        //     echo 'Response:<pre>';
        //     print_r($response);
        //     echo '</pre>';
        // }

        return $response;
    }

    private function getHeaders()
    {
        return "Content-Type: $this->contentType\r\n"
            . "Accept: $this->accept\r\n";
    }

    private function getQueryString($parameters)
    {
        if (null === $parameters) {
            return "";
        }

        $queryString = "?";
        foreach ($parameters as $key => $value) {
            $queryString .= $key . "=" . urlencode($value);
        }

        return $queryString;
    }

}
?>