<?php

class WordLift_TriplesUtils {

	const BNODE_PREFIX = "_:";
	const HASH_PREFIX = "_:md5-";
	
	const TYPE_NAME = "type";
	const DATATYPE_NAME = "datatype";
	const VALUE_NAME = "value";
	const LANGUAGE_NAME = "lang";

	const TYPE_BNODE_NAME = "bnode";


	public function getIndexFromFile( $path ) {

		$parser = ARC2::getRDFParser();
		$parser->parse( $path );

		return ARC2::getSimpleIndex(
			$parser->getTriples(),
			false
		);
	}

	public function bNodesToMD5 ( $index ) {

		return $this->processNodes( $index );
	}

	public function getDifferences( &$predicates, &$referencePredicates ) {
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

	private function processNodes( &$index ) {

		$newIndex = array();
		$bNodesMap = array();

		while ( NULL !== ( $subject = key( $index ) ) )
			$this->processNode( $index, $subject, $newIndex, $bNodesMap );

		return $newIndex;
	}

	private function processNode( &$index, $subject, &$newIndex, &$bNodesMap ) {

		// return an MD5-subject for the requested anonymous bnode.
		if ( array_key_exists( $subject, $bNodesMap ) )
			return $bNodesMap[ $subject ];

		// return nothing if the subject cannot be found in the index (how did we get here?).
		if ( ! array_key_exists( $subject, $index ) )
			return;

		// check if the value references to a bnode.
		$predicates = &$index[ $subject ];
		foreach ( $predicates as $predicate => &$objects ) {
			foreach ( $objects as &$object ) {
				if ( is_array( $object )
					&& array_key_exists( self::TYPE_NAME, $object)
					&& self::TYPE_BNODE_NAME === $object[ self::TYPE_NAME ]
					&& array_key_exists( self::VALUE_NAME, $object )
	 				// we don't take processed bnodes.
					&& ( 0 !== strpos( $object[ self::VALUE_NAME ], self::HASH_PREFIX ) ) ) {

					$bNodeName = $object[ self::VALUE_NAME ];
					$object[ self::VALUE_NAME ] = $this->processNode( $index, $bNodeName, $newIndex, $bNodesMap );
				}
			}
		}

		$newSubject = $subject;
		if ( 0 === strpos( $subject, self::BNODE_PREFIX )
			&& 0 !== strpos( $subject, self::HASH_PREFIX ) ) {

			$newSubject = self::HASH_PREFIX . md5( serialize( $predicates ) );
			$bNodesMap[ $subject ] = $newSubject;
		}

		$newIndex[ $newSubject ] = $predicates;
		unset( $index[ $subject ] );

		return $newSubject;
	}

}

?>