<?php
/**
 * User: david
 * Date: 15/07/12 12:03
 */

class WordLift_ContentFilter {

    // the logger.
    public $logger;

    public $entityService;

    public $schemaOrg;

    public function content( $content ) {
        $postID = get_the_ID();

        $entities = $this->entityService->getEntities( $postID );

        // return the content w/o modifying it if there are not entities.
        if (0 === count($entities))
            return $content;

        $content .= "<div class=\"container entities\"><div class=\"tiles entities\">";

        foreach ($entities as $entity) {

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
