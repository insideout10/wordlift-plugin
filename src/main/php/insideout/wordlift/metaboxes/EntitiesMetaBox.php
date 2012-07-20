<?php
/**
 * User: david
 * Date: 20/07/12 18:57
 */

class WordLift_EntitiesMetaBox implements WordPress_IMetaBox {

    public $logger;

    public $dataStore;
    public $metaKey;
    public $postType;
    public $postStatus;

    public $schemaOrg;

    /**
     * see here for information about the call-back: http://codex.wordpress.org/Function_Reference/add_meta_box#Example
     * @param $post
     */
    public function getHtml( $post ) {
        $this->logger->trace( "Printing out Html.");

        if (NULL === $this->dataStore)
            throw new Exception( "The data-store hasn't been set. Check your configuration." );


        $this->logger->trace( "Getting entities for post ID [$post->ID]." );

        $entityPosts = get_posts( array(
            "numberposts" => -1,
            "offset" => 0,
            "meta_key" => $this->metaKey,
            "meta_value" => $post->ID,
            "post_type" => $this->postType,
            "post_status" => $this->postStatus
        ));

        $this->logger->trace( "Found " . count($entityPosts) . " entity post(s) for post ID [$postID]." );

        // return the content w/o modifying it if there are not entities.
        if (0 === count($entityPosts))
            return $content;

        $content .= "<div class=\"container entities\"><div class=\"tiles entities\">";

        foreach ($entityPosts as $entityPost) {

            $this->logger->trace( "Loading Entity from Entity Post ID [$entityPost->ID]." );
            $entity = new SchemaOrg_Entity(
                $entityPost->ID,
                NULL,
                $this->dataStore);

            $name = $entity->name->getValue(0);
            $type = $entity->getSchema()->getType();
            $url = $entity->url->getValue(0);
            $description = $entity->description->getValue(0);

            $className = strtolower( $type );

            $content .= <<<EOD
<div itemscope="" itemtype="$this->schemaOrg/$type" class="tile entity $className">
    <div itemprop="name" class="name">$name</div>
    <div class="type">$type</div>
    <div itemprop="url" class="url">$url</div>
    <div itemprop="description" class="description">$description</div>
</div>
EOD;

        }

        $content .= "</div></div>";

        echo $content;
    }

}

?>