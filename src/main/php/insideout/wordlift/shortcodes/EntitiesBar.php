<?php
/**
 * User: david
 * Date: 15/07/12 21:23
 */

class WordLift_EntitiesBar {

    public $contentFilterClass;

    public function get( $attributes, $content = NULL) {

        return $this->contentFilterClass->content( $content ) . $content;
    }

}

?>