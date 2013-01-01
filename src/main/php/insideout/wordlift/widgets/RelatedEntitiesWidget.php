<?php

class WordLift_RelatedEntitiesWidget extends WordPress_WidgetProxy {
    /** @var WordLift_EntityService $entityService */
    public $entityService;

    public $queryService;

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            "wordlift_related_entities_widget", // Base ID
            "Entities", // Name
            array( 'description' => "This widget shows the entities." ) // Args
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo "<div class=\"wordlift widget related\"><h4 class=\"widgettitle\">$title</h4><ul>";

        $postID = 29; //  get_the_ID();

        $whereClause = <<<EOF
 
 [] a fise:Enhancement ;
    wordlift:postID "$postID" ;
    wordlift:selected true ;
    fise:entity-reference ?subject .
 ?subject a ?type;
   <http://schema.org/name> ?name ;
   <http://schema.org/description> ?description .
 OPTIONAL { ?subject <http://schema.org/image> ?image }
 FILTER regex( str(?type),  "http://schema.org/" ) 
EOF;

        // public function execute( $fields, $whereClause = NULL, $limit = NULL, $offset = NULL, &$count = NULL, $groupBy = NULL ) {
        $results = $this->queryService->execute( "?subject ?name ?type ?description ?image", $whereClause );
        $rows = &$results[ "result" ][ "rows" ];


        $index = array();
        foreach ( $rows as &$row ) {
            $subject = $row[ "subject" ];
            if ( ! array_key_exists( $subject, $index ) )
                $index[ $subject ] = array( "names" => array(), "types" => array(), "descriptions" => array(), "images" => array() );

            $item = &$index[ $subject ];
            $this->addToIndex( $item, $row, "name" );
            $this->addToIndex( $item, $row, "type" );
            $this->addToIndex( $item, $row, "description" );
            $this->addToIndex( $item, $row, "image" );
        }

        foreach ( $index as &$subject ) {

            echo( "<li itemscope " );
            if ( NULL !== ( $type = $this->getFirstValue( $subject, "types" ) ) )
                echo( " itemtype=\"$type\"" );
            echo( ">" );

            foreach ( $subject[ "names" ] as &$name ) {
                $htmlName = htmlspecialchars( $name[ "value" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                echo( "<div itemprop=\"name\">$htmlName</div>\n" );
            }

            if ( NULL !== ( $image = $this->getFirstValue( $subject, "images" ) ) ) {
                $htmlImage = htmlspecialchars( $image, ENT_COMPAT | ENT_HTML401, "UTF-8" );
                echo( "<div style=\"width: 120px; height: 120px; background-size: contain; background-repeat: no-repeat; background-position: center; background-image: url( '$htmlImage' );\"></div>" );
            } elseif ( NULL !== ( $description = $this->getFirstValue( $subject, "descriptions" ) ) ) {
                $htmlDescription = htmlspecialchars( $subject[ "descriptions" ][0][ "value" ], ENT_COMPAT | ENT_HTML401, "UTF-8" );
                echo( "<div itemprop=\"description\">$htmlDescription</div>" );
            }

            echo( "</li>" );

        }

        echo "</ul></div>";

echo <<<EOF
<script type="text/javascript">
    jQuery( function($) {
        $('.posts.container').arrowscrollers({
            arrow: {
                width: 10
            }
        });
    });
</script>
EOF;

    }

    private function addToIndex( &$subject, &$row, $name ) {
        $var = array(
            "value" => $row[ $name ]
        );

        if ( array_key_exists( "$name lang", $row ) )
            $var[ "lang" ] = $row[ "$name lang" ];

        if ( ! in_array( $var, $subject[ $name . "s" ] ) )
            $subject[ $name . "s" ][] = $var;
    }

    private function getFirstValue( &$array, $key ) {
        if ( 0 === count( $array[ $key ] ) )
            return NULL;

        return $array[ $key ][0][ "value" ];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = strip_tags( $new_instance['title'] );

        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }
        ?>
    <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </p>
    <?php
    }

}

?>