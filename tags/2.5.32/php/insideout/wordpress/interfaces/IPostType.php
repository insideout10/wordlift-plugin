<?php
/**
 * User: david
 * Date: 15/07/12 16:55
 */

interface WordPress_IPostType {

    public function getArguments();
    public function getColumnValue( $column, $postID );
    public function getColumns( $columns );

}

?>