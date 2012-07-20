<?php
/**
 * User: david
 * Date: 15/07/12 12:03
 */

class WordLift_ContentFilter {

    // the logger.
    public $logger;

    public $dataStore;
    public $metaKey;
    public $postType;
    public $postStatus;

    public $schemaOrg;

    public function content( $content ) {
        $postID = get_the_ID();

        if (NULL === $this->dataStore)
            throw new Exception( "The data-store hasn't been set. Check your configuration." );


        $this->logger->trace( "Getting entities for post ID [$postID]." );

        $entityPosts = get_posts( array(
            "numberposts" => -1,
            "offset" => 0,
            "meta_key" => $this->metaKey,
            "meta_value" => $postID,
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

        return $content;
    }
}

?>
