<?php

class TextJobRequest {
	
	public $text;
	public $onCompleteUrl;
	public $onProgressUrl;
	public $chainName;

	private $logger;

	function __construct( $text, $onCompleteUrl, $onProgressUrl, $chainName ) {
		$this->text 		 = $text;
		$this->onCompleteUrl = $onCompleteUrl;
		$this->onProgressUrl = $onProgressUrl;
		$this->chainName	 = $chainName;

		$this->logger 		 = Logger::getLogger(__CLASS__);
		$this->logger->debug("A TextJobRequest has been created [onCompleteUrl:$onCompleteUrl][onProgressUrl:$onProgressUrl][chainName:$chainName].");
	}

}

?>