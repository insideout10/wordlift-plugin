<?php

class WordLift_PropertyAjaxService {
	
	public $queryService;
	public $recordSetService;

	// const NAMESPACE = "dbpedia";

	public function delete( $subject, $requestBody ) {
		$statement = $this->getStatement( $subject, $requestBody );

		$query = <<<EOF
			DELETE {
				$statement
}

EOF;

		$this->queryService->query( $query );
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