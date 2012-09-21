<?php
/**
 * User: David Riccitelli
 * Date: 20/09/12 14:51
 */

class WordLift_TreeMap {

    /** @var WordLift_EntityService $entityService */
    public $entityService;

    public $width;
    public $height;
    public $columnWidth;

    public function get() {

        $rows = $this->entityService->findAll();

        $entities = array();

        foreach ( $rows as $row ) {

            if ( array_key_exists( $row[ "entity" ], $entities ) )
                $entities[ $row[ "entity" ] ][ "count" ] += 1;
            else
                $entities[ $row[ "entity" ] ] = array(
                    "name" => $row[ "name" ],
                    "postID" => $row[ "postID" ],
                    "type" => $row[ "type" ],
                    "count" => 1
                );

        }

        $content = "<div id=\"wordlift\" class=\"treemap\">";

        foreach ( $entities as $entity => $properties ) {
            $name = &$properties[ "name" ];
            $type = &$properties[ "type" ];

            $matches = array();
            preg_match( "/.*\\/(.*)/", $type, $matches ) ;
            $className = ( 0 < count( $matches ) ? strtolower( $matches[ 1 ] ) : "" );

            $count = &$properties[ "count" ];
            if ( 1 < $count )
                $count *= 0.70;

            $width = ( $this->width * $count ) . "px";
            $height = ( $this->height * $count ) . "px";

            $content .= <<<EOF
<div class="entity $className" style="width: $width; height: $height;">
    <div class="symbol"></div>
    <div class="name">$name</div>
    <div class="type">$type</div>
    <div class="count">$count</div>
</div>
EOF;
        }

        $content .= <<<EOF
        </div>

        <script type="text/javascript">
            jQuery( function($) {
                $('#wordlift.treemap').isotope({
                    // options
                    itemSelector : '.entity',
                    layoutMode: 'masonry',
                    masonry: {
                        columnWidth: $this->columnWidth
                    }
                });
            });
        </script>

EOF;

        return $content;

    }

}

?>