<?php

class WordLift_PropertyAjaxService {
	
	public $queryService;
	public $recordSetService;
	public $changeSetService;
	public $triplesUtils;

	// const NAMESPACE = "dbpedia";

	public function delete( $subject, $requestBody ) {
// 		$statement = $this->getStatement( $subject, $requestBody );

// 		$query = <<<EOF
// 			DELETE {
// 				$statement
// }

// EOF;

		// $this->queryService->query( $query );
		$predicate = $property->key;
		$value = $property->value;
		$language = $property->lang;
		$type = $property->type;

		$subject = "http://dbpedia.org/resource/$subject";

		$property = json_decode( $requestBody );
		$index = array( $subject => array(
				$predicate => array( array(
						"value" => $value,
						"type" => $type
				) )
		) );

		if ( ! empty( $language ) )
			$index[ $subject ][ $predicate ][ 0 ][ "lang" ] = $language

        // $index = $this->triplesUtils->getIndexFromData( $requestBody );
        $newIndex = $this->triplesUtils->bNodesToMD5( $index );
        $this->changeSetService->applyChanges( $newIndex, "user", false, "User request" );

	}

	public function save( $subject, $requestBody ) {

		$statement = $this->getStatement( $subject, $requestBody );

		$query = <<<EOF
			INSERT INTO <http://example.org/> {
				$statement
}

EOF;

		$this->queryService->query( $query );
	}

	private function getStatement( $subject, &$requestBody ) {

		$property = json_decode( $requestBody );
		$key = $property->key;
		$value = $property->value;
		$language = $property->lang;
		$type = $property->type;

		switch ( $type ) {
			case "uri":
			case "bnode":
				$value = "<$value>";
				break;
			default:
				$value = "\"$value\"";
		}

		if ( ! empty($language) )
			$value .= "@$language";

		return "<http://dbpedia.org/resource/$subject> <$key> $value";
	}

}

?>