<?php
/**
 * User: David Riccitelli
 * Date: 20/09/12 18:53
 */

class WordPress_WidgetProxy extends WP_Widget {

    /** @var WordPress_XmlApplication $applicationContext */
    public static $applicationContext;

    function __construct( $id_base = false, $name, $widget_options = array(), $control_options = array() ) {
        parent::__construct( $id_base, $name, $widget_options, $control_options );

        $classID = self::$applicationContext->getClassID( get_class( $this ) );
        self::$applicationContext->getClass( $classID, NULL, NULL, $this );
    }
}

?>