<?php

class WordLift_HttpOperations
{
    const CONTENT_TYPE_JSON = "application/json";

    const DEFAULT_TIMEOUT = 60;

    private $apiUrl;
    private $contentType;
    private $accept;
    private $headers;
    private $timeout;

    public static function create(
        $apiUrl,
        $contentType,
        $accept,
        $headers = null,
        $timeout = self::DEFAULT_TIMEOUT
    ) {

        return new self(
            $apiUrl,
            $contentType,
            $accept,
            $headers,
            $timeout
        );
    }

    function __construct(
        $apiUrl,
        $contentType,
        $accept,
        $headers = null,
        $timeout = self::DEFAULT_TIMEOUT
    ) {
        $this->apiUrl = $apiUrl;
        $this->contentType = $contentType;
        $this->accept = $accept;
        $this->headers = $headers;
        $this->timeout = $timeout;
    }

    public function post($parameters, $body)
    {
        $url = $this->apiUrl . $this->getQueryString($parameters);

        $options = array(
            "timeout" => $this->timeout,
            "headers" => $this->getHeaders(),
            "body" => $body,
            "sslverify" => false
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
            "headers" => $this->getHeaders(),
            "sslverify" => false
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
        $headers = "Content-Type: $this->contentType\r\n"
            . "Accept: $this->accept\r\n";

        if (null === $this->headers) {
            return $headers;
        }

        foreach ($this->headers as $key => $value) {
            $headers .= "$key: $value\r\n";
        }

        return $headers;
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