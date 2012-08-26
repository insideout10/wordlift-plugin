<?php
/**
 * User: david
 * Date: 26/08/12 15:16
 */

class WordLift_TripleStoreService {

    // the table prefix for the triple store tables.
    public $tablePrefix;

    /**
     * Get the triple store.
     * @return ARC2_Store A triple store.
     */
    public function getStore() {

        /** @var ARC2_Store $store */
        $store = ARC2::getStore(array(
            "ns" => array(
                "rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
                "rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
                "dbpedia" => "http://dbpedia.org/ontology/",
                "schema" => "http://schema.org/",
                "fise" => "http://fise.iks-project.eu/ontology/",
                "wordlift" => "http://purl.org/insideout/wordpress/"
            ),

            "db_host" => DB_HOST,
            "db_name" => DB_NAME,
            "db_user" => DB_USER,
            "db_pwd" => DB_PASSWORD,
            "store_name" => $this->tablePrefix
        ));

        if (!$store->isSetUp()) {
            $store->setUp();
        }

        return $store;
    }

    /**
     * Get an RDF parser.
     * @return ARC2_RDFParser The RDF parser.
     */
    public function getRDFParser() {

        /** @var ARC2_RDFParser $parser */
        $parser = ARC2::getRDFParser();

        return $parser;
    }

}

?>