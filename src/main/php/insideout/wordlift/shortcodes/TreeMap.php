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

        $content =<<<EOF

<div class="treemap toolbar">
    <div class="selector person" data-filter="">All</div>
    <div class="selector person" data-filter=".person"><div class="symbol"></div>Person</div>
    <div class="selector organization" data-filter=".organization"><div class="symbol"></div>Organization</div>
    <div class="selector place" data-filter=".place"><div class="symbol"></div>Place</div>
    <div class="selector event" data-filter=".event"><div class="symbol"></div>Event</div>
    <!-- div class="selector product" data-filter="product"><div class="symbol"></div>Product</div -->
    <div class="selector creativework data-filter=".creativework"><div class="symbol"></div>Creative Works</div>
</div>

<div id="wordlift" class="treemap isotope">
EOF;

        foreach ( $entities as $entity => $properties ) {
            $name = &$properties[ "name" ];
            $type = &$properties[ "type" ];

            $matches = array();
            preg_match( "/.*\\/(.*)/", $type, $matches ) ;
            $className = ( 0 < count( $matches ) ? strtolower( $matches[ 1 ] ) : "" );

            $count = &$properties[ "count" ];
            if ( 8 < $count )
                $count = 8;

            if ( 1 < $count && 4 >= $count )
                $count *= 0.60;

            if ( 4 < $count )
                $count *= 0.40;



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

                $('.treemap.toolbar .selector')
                    .click( function( event ) {
                    console.log( event );
                        $('#wordlift.treemap')
                            .isotope({
                                filter: $( event.target ).data('filter')
                            });
                    });
            });
        </script>

EOF;

        return $content;

    }

}

?>