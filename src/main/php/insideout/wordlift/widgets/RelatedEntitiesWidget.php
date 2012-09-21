<?php

class WordLift_RelatedEntitiesWidget extends WordPress_WidgetProxy {
    /** @var WordLift_EntityService $entityService */
    public $entityService;

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

        $related = $this->entityService->findRelated( get_the_ID() );
        $entities = &$related[ "entities" ];
        foreach( $entities as $entity => $properties ) {
            $name = htmlentities( $properties["names"][0] );
            $type = htmlentities( $properties["types"][0] );
            $matches = array();
            preg_match( "/.*\\/(.*)/", $type, $matches ) ;
            $className = ( 0 < count( $matches ) ? strtolower( $matches[ 1 ] ) : "" );

            echo "<li class=\"entity $className\"><div class=\"symbol\"></div>$name</li>";
//            echo "<li><a href=\"" . get_permalink( $postID ) . "\">" . get_the_title( $postID ) . "</a></strong><br/>";
//
//            foreach( $properties[ "entities" ] as $entity => $properties ) {
//                if ( 0 < count( $properties[ "images" ] ) && !empty( $properties[ "images" ][0] ) ) {
//                    $title = htmlentities( $properties[ "names" ][0] );
//                    echo "<img title=\"$title\" class=\"entity image\" src=\"" . $properties[ "images" ][0] . "\"
//                        onerror=\"this.parentNode.removeChild(this);\"/>";
//                }
//            }
            echo "</li>";

        }

        echo "</ul></div>";
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