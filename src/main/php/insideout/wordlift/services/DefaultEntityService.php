<?php

/**
 * This class provide read/write access to the entities in this blog. 
 */
class WordLift_DefaultEntityService implements WordLift_EntityService {

	// The logger instance.
	public $logger;

    public $dataStore;
    public $metaKey;
    public $postType;
    public $postStatus;

    public $metaKeySubject;
    public $metaKeyReferences;

    /** @var WordLift_TripleStoreService $tripleStoreService */
    public $tripleStoreService;

    public function findAll() {

        $query = "SELECT DISTINCT ?entity ?postID ?name ?type
                  WHERE {
                    ?textAnnotation a fise:TextAnnotation .
                    ?textAnnotation wordlift:postID ?postID .
                    ?entityAnnotation a fise:EntityAnnotation .
                    ?entityAnnotation dcterms:relation ?textAnnotation .
                    ?entityAnnotation fise:entity-reference ?entity .
                    ?entityAnnotation wordlift:selected true .
                    ?entity a ?type .
                    ?entity schema:name ?name .
                  }";

        $result = $this->tripleStoreService->query( $query );
        $rows = &$result[ "result" ][ "rows" ];

        return $rows;

    }

    public function findRelated( $postID ) {

        $query =<<<EOF
            SELECT DISTINCT ?postID ?entity ?type ?name ?image
                WHERE {
                    ?entity a ?type;
                        schema:name ?name .
                    ?textAnnotation a fise:TextAnnotation;
                        wordlift:postID "$postID" .
                    ?entityAnnotation a fise:EntityAnnotation;
                        dcterms:relation ?textAnnotation;
                        fise:entity-reference ?entity;
                        wordlift:selected true .
                    ?entityAnnotations fise:entity-reference ?entity;
                        dcterms:relation ?textAnnotations;
                        wordlift:selected true .
                    ?textAnnotations wordlift:postID ?postID .
                OPTIONAL { ?entity schema:image ?image }
                FILTER( ?postID != "$postID" )
            }
            ORDER BY DESC( ?postID )
EOF;

        $result = $this->tripleStoreService->query( $query );
        $rows = &$result[ "result" ][ "rows" ];

        $related = array(
            "entities" => array(),
            "posts" => array()
        );
        $entities = &$related[ "entities" ];
        $posts = &$related[ "posts" ];

        foreach ( $rows as $row ) {

            if ( ! array_key_exists( $row[ "postID" ], $posts ) )
                $posts[ $row[ "postID" ] ] = array(
                    "entities" => array()
                );

            $post = &$posts[ $row["postID"] ];

            if ( ! array_key_exists( $row[ "entity" ], $entities ) )
                $entities[ $row[ "entity" ] ] = array(
                    "images" => array(),
                    "names" => array(),
                    "types" => array(),
                    "posts" => array()
                );

            $entity = &$entities[ $row[ "entity" ] ];

            if ( ! in_array( $row[ "postID" ], $entity[ "posts" ] ) )
                $entity[ "posts" ][] = $row[ "postID" ];

            if ( ! in_array( $row[ "entity" ], $post[ "entities" ] ) )
                $post[ "entities" ][] = $row[ "entity" ];

            if ( ! empty( $row[ "image" ] ) && ! in_array( $row[ "image" ], $entity[ "images" ] ) )
                $entity[ "images" ][] = $row[ "image" ];

            if ( ! empty( $row[ "name" ] ) && ! in_array( $row[ "name" ], $entity[ "names" ] ) )
                $entity[ "names" ][] = $row[ "name" ];

            if ( ! empty( $row[ "type" ] ) && ! in_array( $row[ "type" ], $entity[ "types" ] ) )
                $entity[ "types" ][] = $row[ "type" ];

        }

        return $related;
    }

    public function getByPostID( $postID ) {
        return get_post_custom_values( $this->metaKeyReferences, $postID );
    }

    public function getBySubject( $subject ) {

        return get_posts( array(
            "numberposts" => 1,
            "post_type" => $this->postType,
            "meta_key" => $this->metaKeySubject,
            "meta_value" => $subject,
            "post_status" => "any"
        ));

    }

    public function create( $subject ) {
        $error = null;

        $post = array(
            "post_type" => $this->postType
        );

        $postID = wp_insert_post( $post, $error );

        if ( is_wp_error( $error ) ) {
            $this->logger->error( "An error occured while creating a post [ subject :: $subject ]:\n$error->get_error_message()" );
            return;
        }

        add_post_meta( $postID , $this->metaKeySubject , $subject );

        $this->logger->trace( "A new post has been created [ postType :: $this->postType ][ subject :: $subject ][ postID :: $postID ]." );

    }

    public function bindPostToSubjects( $postID, $subject ) {
        $references = get_post_custom_values( $this->metaKeyReferences, $postID );

        // return if a reference is set already.
        if ( is_array( $references ) && in_array( $subject, $references ) ) return;

        if ( false === add_post_meta( $postID , $this->metaKeyReferences , $subject ) )
            $this->logger->error( "An error occured while binding a post to an entity [ postID :: $postID ][ metaKeyReferences :: $this->metaKeyReferences ][ subject :: $subject ]." );
    }

    public function getPosts( $postID ) {
        $this->logger->trace( "Getting entities for post ID [$postID]." );

        $posts = get_posts( array(
            "numberposts" => -1,
            "offset" => 0,
            "meta_key" => $this->metaKey,
            "meta_value" => $postID,
            "post_type" => $this->postType,
            "post_status" => $this->postStatus
        ));

        $this->logger->trace( "Found " . count($posts) . " entity post(s) for post ID [$postID]." );

        return $posts;
    }

    public function getEntities( $postID ) {

        if (NULL === $this->dataStore)
            throw new Exception( "The data-store hasn't been set. Check your configuration." );

        $posts = $this->getPosts( $postID );

        $entities = array();
        foreach ($posts as &$post) {
            $this->logger->trace( "Loading Entity from Entity Post ID [$post->ID]." );

            array_push( $entities,
                new SchemaOrg_Entity(
                    $post->ID,
                    NULL,
                    $this->dataStore)
            );

        }

        return $entities;
    }
}

?>