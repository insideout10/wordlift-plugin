<?php
/**
 * User: david
 * Date: 23/08/12 16:12
 */


class WordLift_JobCallbackService {

    public $logger;

    public function callback( $jobID, $requestBody ) {
        $this->logger->trace( "A message has been received [ jobID :: $jobID ][ requestBody :: $requestBody ]." );

        $parser = ARC2::getRDFParser();
        $parser->parseData( $requestBody );
        $triples = $parser->getTriples();

        $this->logger->trace( count( $triples ) . " triple(s) found." );

        $index = $parser->getSimpleIndex(0);

        foreach ( $index as $subject => $predicates ) {
            $predicatesCount = count( $predicates );

            $this->logger->trace( "[ subject :: $subject ][ predicatesCount :: $predicatesCount ]" );

            foreach ( $predicates as $predicate => $objects ) {
                $objectsCount = count( $objects );

                $this->logger->trace( "   [ predicate :: $predicate ][ objectsCount :: $objectsCount ]" );

                foreach ( $objects as $object ) {
                    $type = $object[ "type" ];
                    $value = $object[ "value" ];

                    $this->logger->trace( "      [ value :: $value ][ type :: $type ]" );
                }
            }
        }
    }

}

?>