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

    public function getBySubject( $subject ) {

        return get_posts( array(
            "numberofposts" => 1,
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