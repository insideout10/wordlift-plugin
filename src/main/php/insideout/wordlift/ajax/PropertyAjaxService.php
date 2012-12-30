<?php

class WordLift_PropertyAjaxService {
	
	public $queryService;
	public $recordSetService;
	public $changeSetService;
	public $triplesUtils;

	const TYPE_DELETE = "delete";
	const TYPE_UPDATE = "update";

	// const NAMESPACE = "dbpedia";

	public function delete( $subject, $requestBody ) {
		$this->saveToStore( $subject, $requestBody, self::TYPE_DELETE );
	}

	public function save( $subject, $requestBody ) {
		$this->saveToStore( $subject, $requestBody, self::TYPE_UPDATE );
	}

	private function saveToStore( $subject, $requestBody, $actionType ) {

		$property = json_decode( $requestBody );
		$predicate = $property->key;
		$value = $property->value;
		$language = $property->lang;
		$type = $property->type;
		$datatype = $property->datatype;

		$subject = "http://dbpedia.org/resource/$subject";

		$predicates = array(
				$predicate => array( array(
						"value" => $value,
						"type" => $type
				) )
		);

		if ( ! empty( $language ) )
			$predicates[ $predicate ][ 0 ][ "lang" ] = $language;
		if ( ! empty( $datatype ) )
			$predicates[ $predicate ][ 0 ][ "datatype" ] = $datatype;

        $currentUser = wp_get_current_user();
        $currentUserName = $currentUser->user_login;

        switch ( $actionType ) {
        	case self::TYPE_DELETE:
        		$this->changeSetService->saveChanges( $subject, $currentUserName, array(), $predicates, "User request" );	
        		break;
        	case self::TYPE_UPDATE:
				$this->changeSetService->saveChanges( $subject, $currentUserName, $predicates, array(), "User request" );	
				break;
        }
        
	}

	// private function getStatement( $subject, &$requestBody ) {

	// 	$property = json_decode( $requestBody );
	// 	$key = $property->key;
	// 	$value = $property->value;
	// 	$language = $property->lang;
	// 	$type = $property->type;

	// 	switch ( $type ) {
	// 		case "uri":
	// 		case "bnode":
	// 			$value = "<$value>";
	// 			break;
	// 		default:
	// 			$value = "\"$value\"";
	// 	}

	// 	if ( ! empty($language) )
	// 		$value .= "@$language";

	// 	return "<http://dbpedia.org/resource/$subject> <$key> $value";
	// }

}

?>