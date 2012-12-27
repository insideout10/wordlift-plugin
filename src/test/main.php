<?php

const RDF_TYPE = "http://www.w3.org/1999/02/22-rdf-syntax-ns#type";
const RDF_SEQ = "rdf:Seq";
const RDF_LI = "rdf:li";
const RDF_VALUE = "rdf:value";
const BNODE_PREFIX = "_:";
const HASH_PREFIX = "md5-";
const GRAPH_URI = "http://wordlift.it/graph";

const LANGUAGE_NAME = "lang";
const VALUE_NAME = "value";
const TYPE_NAME = "type";
const DATATYPE_NAME = "datatype";
const TYPE_BNODE_NAME = "bnode";

const DELETE_FORM_NAME = "DELETE";
const INSERT_FORM_NAME = "INSERT";

const CHANGESET_TYPE_URI = "http://purl.org/vocab/changeset/schema#ChangeSet";
const CHANGESET_SUBJECT_OF_CHANGE_URI = "http://purl.org/vocab/changeset/schema#subjectOfChange";
const CHANGESET_CREATED_DATE_URI = "http://purl.org/vocab/changeset/schema#createdDate";
const CHANGESET_CREATOR_NAME_URI = "http://purl.org/vocab/changeset/schema#creatorName";
const CHANGESET_CHANGE_REASON_URI = "http://purl.org/vocab/changeset/schema#changeReason";
const CHANGESET_REMOVAL_URI = "http://purl.org/vocab/changeset/schema#removal";
const CHANGESET_ADDITION_URI = "http://purl.org/vocab/changeset/schema#addition";
const RDF_STATEMENT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement";
const RDF_SUBJECT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#subject";
const RDF_PREDICATE_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#predicate";
const RDF_OBJECT_URI = "http://www.w3.org/1999/02/22-rdf-syntax-ns#object";
const RDFS_DATE_TIME_DATATYPE_URI = "http://www.w3.org/2001/XMLSchema#dateTime";

const CHANGE_ADDITION_NAME = "addition";
const CHANGE_REMOVAL_NAME = "removal";

include_once( "php/arc2/ARC2.php" );
include_once( "ChangeSet.php" );
include_once( "ChangeSetService.php" );

$changeSetService = new ChangeSetService();

$parser = ARC2::getRDFParser();
// $parser->parse( "changeset.rdf" );
$parser->parse( "resources/sample-4.rdf" );
$triples = $parser->getTriples();

$index = ARC2::getSimpleIndex($triples, false );
$newIndex = processNodes( $index );

$store = getStore();

foreach ( $newIndex as $subject => $predicates ) {
	$existingPredicates = getResourcePredicates( $subject, $store );

	if ( md5( serialize( $existingPredicates ) ) !== md5( serialize( $predicates ) ) ) {
		
		$additions = getDifferences( $predicates, $existingPredicates );
		$removals = getDifferences( $existingPredicates, $predicates );

		if ( 0 === count( $additions )
			&& 0 === count( $removals ) ) {

			continue;
		}

		echo( "changes detected [ subject :: $subject ][ additions # :: " . count( $additions ) . " ][ removals :: " . count( $removals ) . " ].\n" );

		if ( 0 < count( $additions ) ) {
			$insertStatement = createStatement( array( $subject => $additions ), INSERT_FORM_NAME, GRAPH_URI );

			// echo("======== INSERT ========\n");
			// echo( $insertStatement );
			// echo("======== /INSERT =======\n");

			// query( $store, $insertStatement );
		}

		if ( 0 < count( $removals ) ) {
			$deleteStatement = createStatement( array( $subject => $removals ), DELETE_FORM_NAME, GRAPH_URI );

			// echo("======== DELETE ========\n");
			// echo( $deleteStatement );
			// echo("======== /DELETE =======\n");

			// query( $store, $deleteStatement );
		}

		$changeSetStatement = createChangeSetStatement( $subject, date_create(), "auto", "tests", $removals, $additions, GRAPH_URI );

		echo( "======== CHANGESET =========\n" );
		echo( $changeSetStatement );
		echo( "======== /CHANGESET ========\n" );

		query( $store, $changeSetStatement );

		// echo( "======== ADDITIONS =========\n" );
		// var_dump( $additions );
		// echo( "======== /ADDITIONS ========\n" );
		// echo( "======== REMOVALS ==========\n" );
		// var_dump( $removals );
		// echo( "======== /REMOVALS =========\n" );
	}
}

function createChangeSetStatement( $subjectOfChange, $createdDate, $creatorName, $changeReason, $removals, $additions, $namespace ) {

	$subjectOfChange = escapeValue( $subjectOfChange );
	$creatorName = escapeValue( $creatorName );
	$changeReason = escapeValue( $changeReason );
	
	$createdDate = date_format( $createdDate, "Y-m-d\TH:i:s\Z" );

	$subject = BNODE_PREFIX . uniqid( "changeset-" );
	$statement = "INSERT INTO <$namespace> {\n";
	$statement .= "<$subject> <" . RDF_TYPE . "> <" . CHANGESET_TYPE_URI . "> ; \n";
	$statement .= "           <" . CHANGESET_SUBJECT_OF_CHANGE_URI . "> <$subjectOfChange> ; \n";
	$statement .= "           <" . CHANGESET_CREATED_DATE_URI . "> \"$createdDate\"^^<" . RDFS_DATE_TIME_DATATYPE_URI . "> ; \n";
	$statement .= "           <" . CHANGESET_CREATOR_NAME_URI . "> \"$creatorName\" ; \n";
	$statement .= "           <" . CHANGESET_CHANGE_REASON_URI . "> \"$changeReason\" . \n";

	if ( 0 < count( $additions ) )
		$statement .= getChangeSetChanges( $subject, $subjectOfChange, $additions, CHANGE_ADDITION_NAME );

	if ( 0 < count( $removals ) )
		$statement .= getChangeSetChanges( $subject, $subjectOfChange, $removals, CHANGE_REMOVAL_NAME ); 

	$statement .= "}";

	return $statement;
}

function getChangeSetChanges( $subject, $subjectOfChange, $changes, $changeType ) {

	switch ( $changeType ) {
		case CHANGE_ADDITION_NAME:
			$type = CHANGESET_ADDITION_URI;
			break;
		case CHANGE_REMOVAL_NAME:
			$type = CHANGESET_REMOVAL_URI;
			break;
	}

	$statement = "";
	if ( 0 < count( $changes ) ) {
		foreach ( $changes as $predicate => $objects ) {
			foreach ( $objects as $object ) {
				$changeSubject = BNODE_PREFIX . uniqid( "changeset-$changeType-" );
				$statement .= "<$subject> <$type> <$changeSubject> . \n";
				$statement .= "<$changeSubject> <" . RDF_TYPE . "> <" . RDF_STATEMENT_URI . "> ; \n";
				$statement .= "                   <" . RDF_SUBJECT_URI . "> <$subjectOfChange> ; \n";
				$statement .= "                   <" . RDF_PREDICATE_URI . "> <$predicate> ; \n";
				$statement .= getPredicateAndObject( RDF_OBJECT_URI, $object ) . " . \n";
			}
		}
	}	

	return $statement;
}

function query( &$store, $statement ) {
	$result = $store->query( $statement, "raw", "", true );

	if ($errs = $store->getErrors()) {
		echo( "ERROR\n" );
	}
}

function escapeValue( $value ) {
	$value = str_replace( "\\", "\\\\", $value );
	$value = str_replace( "\"", "\\\"", $value ); 
	$value = str_replace( "'", "\\'", $value );

	return $value;
}

function createStatement( $array, $form, $namespace = "" ) {

	$statement = "";

	switch ( $form ) {
		case DELETE_FORM_NAME:
			$statement .= "DELETE FROM <$namespace> {\n";
			break;
		case INSERT_FORM_NAME:
			$statement .= "INSERT INTO <$namespace> {\n";
			break;
		default:
	}

	foreach ( $array as $subject => &$predicates ) {
		foreach ( $predicates as $predicate => &$objects ) {
			foreach ( $objects as $object ) {
				$statement .= "<$subject> ";

				// $type = $object[ TYPE_NAME ];
				// $datatype = ( array_key_exists( DATATYPE_NAME, $object ) ? escapeValue( $object[ DATATYPE_NAME ] ) : "" );
				// $value = escapeValue( $object[ VALUE_NAME ] );
				// $language = ( array_key_exists( LANGUAGE_NAME, $object ) ? "@" . $object[ LANGUAGE_NAME ] : "" );

				// switch ( $type ) {
				// 	case "bnode":
				// 	case "uri":
				// 		$statement .= "<$predicate> <$value> ";
				// 		break;
				// 	default:
				// 		if ( empty( $datatype ) ) 
				// 			$statement .= "<$predicate> \"$value\"$language ";
				// 		else
				// 			$statement .= "<$predicate> \"$value\"^^<$datatype> ";
				// 		break;
				// }

				$statement .= getPredicateAndObject( $predicate, $object ) . ". \n";
			}
		}
	}

	switch ( $form ) {
		case DELETE_FORM_NAME:
			$statement .= "}\n";
			break;
		case INSERT_FORM_NAME:
			$statement .= "}\n";
			break;
		default:
	}

	return $statement;
}

function getPredicateAndObject( $predicate, $object ) {
	$type = $object[ TYPE_NAME ];
	$datatype = ( array_key_exists( DATATYPE_NAME, $object ) ? escapeValue( $object[ DATATYPE_NAME ] ) : "" );
	$value = escapeValue( $object[ VALUE_NAME ] );
	$language = ( array_key_exists( LANGUAGE_NAME, $object ) ? "@" . $object[ LANGUAGE_NAME ] : "" );

	switch ( $type ) {
		case "bnode":
		case "uri":
			return "<$predicate> <$value> ";
			break;
		default:
			if ( empty( $datatype ) ) 
				return "<$predicate> \"$value\"$language ";
			else
				return "<$predicate> \"$value\"^^<$datatype> ";
			break;
	}

	return "";
}

function getDifferences( &$predicates, &$referencePredicates ) {
	$differences = array();

	foreach ( $predicates as $predicate => &$objects ) {
		if ( ! array_key_exists( $predicate, $referencePredicates ) ) {
			$differences[ $predicate ] = $objects;
			continue;
		}

		foreach ( $objects as &$object ) {

			$found = false;
			foreach ( $referencePredicates[ $predicate ] as $referenceObject ) {
				if ( md5( serialize( $object ) ) ===  md5( serialize( $referenceObject ) ) ) {
					$found = true;
					break;
				}
			}

			if ( $found ) // on to the next object, if this one has been found.
				continue;
			else { // add the object if not found.
				$differences[ $predicate ][] = $object;
			}
		}
	}

	return $differences;
}

function getStore() {
	$store = ARC2::getStore( getConfig() );
	// $store->drop();

	if (!$store->isSetUp())
		$store->setUp();

	return $store;
}

function getConfig() {
	return array(
		'ns' => array(
		  'rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
		  'rdfs' => 'http://www.w3.org/2000/01/rdf-schema#',
		  'dbpedia' => 'http://dbpedia.org/ontology/'
		),
		/* db */
		'db_host' => '127.0.0.1', /* default: localhost */
		'db_name' => 'arc2_dev',
		'db_user' => 'arc2',
		'db_pwd' => 'arc2pwd',
		/* store */
		'store_name' => 'arc2',
		/* network */
		// 'proxy_host' => '192.168.1.1',
		// 'proxy_port' => 8080,
		/* parsers */
		'bnode_prefix' => 'bn',
		/* sem html extraction */
		'sem_html_formats' => 'rdfa microformats',
	);
}

function getResourcePredicates( $subject, &$store ) {
	$resource = ARC2::getResource();
	$resource->setStore( $store );
	$resource->setURI( $subject );

	$properties = $resource->getProps();

	return $properties;
}

function processNodes( $index ) {
	$newIndex = array();
	$bNodesMap = array();

	while ( NULL !== ( $subject = key( $index ) ) )
		processNode( $index, $subject, $newIndex, $bNodesMap );

	return $newIndex;
}

function processNode( &$index, $subject, &$newIndex, &$bNodesMap ) {
	// echo( "processing [ subject :: $subject ]...\n" );

	if ( array_key_exists( $subject, $bNodesMap ) ) {
		// echo( "node already processed [ subject :: $subject ].\n" );
		return $bNodesMap[ $subject ];
	} 

	if ( ! array_key_exists( $subject, $index ) ) {
		// echo( "subject not found [ subject :: $subject ].\n" );
		return;
	}

	$predicates = &$index[ $subject ];

	// check if the value references to a bnode.
	foreach ( $predicates as $predicate => &$objects ) {
		foreach ( $objects as &$object ) {
			if ( is_array( $object )
				&& array_key_exists( TYPE_NAME, $object)
				&& TYPE_BNODE_NAME === $object[ TYPE_NAME ]
				&& array_key_exists( VALUE_NAME, $object )
 				// we don't take processed bnodes.
				&& ( 0 !== strpos( $object[ VALUE_NAME ], BNODE_PREFIX . HASH_PREFIX ) ) ) {

				$bNodeName = $object[ VALUE_NAME ];
				// echo( "found a BNode [ bNodeName :: $bNodeName ].\n" );

				$object[ VALUE_NAME ] = processNode( $index, $bNodeName, $newIndex, $bNodesMap );
				// echo( "replacing [ nNodeName :: $bNodeName ] with [ new :: " . $object[ VALUE_NAME ] . " ].\n" );
			}
		}
	}

	$newSubject = $subject;
	if ( 0 === strpos( $subject, BNODE_PREFIX )
		&& 0 !== strpos( $subject, BNODE_PREFIX . HASH_PREFIX ) ) {

		$newSubject = BNODE_PREFIX . HASH_PREFIX . md5( serialize( $predicates ) );
		$bNodesMap[ $subject ] = $newSubject;

		// echo( "assigning new subject [ subject :: $subject ][ newSubject :: $newSubject ].\n" );
	}

	$newIndex[ $newSubject ] = $predicates;
	unset( $index[ $subject ] );

	return $newSubject;
}

exit;



$entities = array();

$keysReferencingAnonymousNode = getKeysReferencingAnonymousNode( $index );
$anonymousNodesMap = array();



replaceURIs( $index, $anonymousNodesMap );

unset( $index );

var_dump($entities);

function replaceURIs( &$array, $anonymousNodesMap ) {
	foreach ( $array as $subject => &$predicates ) {
		foreach ( $predicates as $predicate => &$objects ) {
			foreach ( $objects as &$object ) {
				if ( "bnode" === $object[ "type" ] ) {
					$bnode = $object[ "value" ];
					echo "replacing bnode [ $bnode ].\n";
					$object[ "value" ] = $anonymousNodesMap[ $bnode ];
				}
			}
		}
	}
}

function getKeysReferencingAnonymousNode( $array ) {
	$keys = array();

	foreach ( $array as $key => $value )
		if ( ( is_array( $value ) && 0 < count( getKeysReferencingAnonymousNode( $value ) ) )
			|| ( ! is_array( $value ) && 0 === strpos( $value, BNODE_PREFIX ) ) ) {

			$keys[] = $key;
		}

	return $keys;
}

function getNewKey( $key, $value, &$anonymousNodesMap ) {
	if ( 0 === strpos( $key, BNODE_PREFIX ) ) {

		if ( array_key_exists( $key, $anonymousNodesMap ) )
			return $anonymousNodesMap[ $key ];

		$newKey = BNODE_PREFIX . HASH_PREFIX. md5( serialize( $value ) );
		$anonymousNodesMap[ $key ] = $newKey;
		return $newKey;
	}

	return $key;
}
exit;

$store = getStore();

while ( NULL !== ( $key = key( $index ) ) ) {
	$newPredicates = $index[ $key ];

	// move next if the entity is not supported.
	if ( ! isSupported( $key, $newPredicates ) ) {
		next( $index );
		continue;
	}

	// check if this entity is already in the store.
	if ( FALSE === ( $predicates = getEntityPredicates( $key, $store ) ) ) {
		echo "$key does not exists, creating.\n";
		$store->insert( array( $key => $entity ), GRAPH_URI );
	}
	else {
		$additions = entityDiff( $newPredicates, $predicates );
		$removals = entityDiff( $predicates, $newPredicates );

		$changeSetService->create( $key, "auto", "this is the reason", $additions, $removals);
	}

	next( $index );
}

function entityDiff( $source, $search ) {
	$additions = array();

	foreach ( $source as $predicate => $objects ) {
		if ( ! array_key_exists( $predicate, $search ) ) {
			$additions[] = array( $predicate => $objects );
		}
		else {
			foreach ( $objects as $object ) {
				$hash = md5( serialize( $object ) );	
		
				$found = FALSE;
				foreach ( $search[ $predicate ] as $searchObject ) {
					if ( $hash === md5( serialize( $searchObject ) ) ) {
						$found = TRUE;
						break;
					}
				}

				if ( ! $found ) {
					$additions = array_merge_recursive( $additions, array( $predicate => array( $object ) ) );
				}
			}
		}
	}

	return $additions;
}

function isSupported( $key, $entity ) {
	// return not supported, if it's a bnode.
	if ( 0 === strpos( $key, BNODE_PREFIX ) )
		return false;

	// return not supported, if the type is inexistent.
	if ( ! array_key_exists( RDF_TYPE, $entity ) )
		return false;

	// check for supported types.
	$types = &$entity[ RDF_TYPE ];
	foreach ( $types as $type ) {
		if ( array_key_exists( "value", $type ) 
			&& 1 === preg_match( "/^http:\/\/schema.org\//", $type[ "value" ] ) )

			return true;
	}

	return false;
}



// function createStatement( $subject, &$predicates ) {

// 	$statements = array();

// 	foreach ( $predicates as $predicate => $objects ) {
// 		foreach ( $objects as $object ) {
// 			$statements[] = "<$subject> <$predicate> " . getVersionedObject( $object ) . " . \n";
// 		}
// 	}

// 	return $statements;
// }

function getVersionedObject( &$object ) {
	$subject = uniqid( BNODE_PREFIX );
	$objectId = uniqid( BNODE_PREFIX );

	$statement  = "<$subject> . \n";
	$statement .= "<$subject> <" . RDF_TYPE . "> <" . RDF_SEQ . "> ; \n";
	$statement .= "           <" . RDF_LI . ">   <$objectId> .  \n";
	$statement .= "<$objectId>  <" . RDF_VALUE . "> " .getObject( $object ) . "\n";

	return $statement;
}

function getObject( &$object ) {

	$type = &$object[ "type" ];
	$value = &$object[ "value" ];
	$language = ( array_key_exists( "lang", $object ) ? "@" . $object[ "lang" ] : "" );

	switch ( $type ) {
		case "uri":
		case "bnode":
			return "<$value>";
			break;
		
		case "literal":
			return escapeValue($value) . "$language";
			break;
	}

	return escapeValue($value) . "^^$type";
}

// function escapeValue( $value ) {
// 	$value = str_replace( "\\", "\\\\", $value );
// 	$value = str_replace( "\"", "\\\"", $value ); 
// 	$value = str_replace( "'", "\\'", $value );

// 	return "\"" . $value . "\"";
// }


?>