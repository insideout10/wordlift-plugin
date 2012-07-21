<?php
/**
 * User: david
 * Date: 20/07/12 18:57
 */

class WordLift_EntitiesMetaBox implements WordPress_IMetaBox {

    public $logger;

    public $entityService;

    public $schemaOrg;

    /**
     * see here for information about the call-back: http://codex.wordpress.org/Function_Reference/add_meta_box#Example
     * @param $post
     */
    public function getHtml( $post ) {
        $this->logger->trace( "Printing out Html.");

        $entities = $this->entityService->getEntities( $post->ID );

        // return the content w/o modifying it if there are not entities.
        if (0 === count($entities))
            return;

        $content = "<div class=\"container entities\"><div class=\"tiles entities\">";

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

        echo $content;
    }

}

?>