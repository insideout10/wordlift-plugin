<?php

class WordPress_HtmlMetaBox implements WordPress_IMetaBox {

    const PARENT = "/wp-content/plugins";

    public $htmlFilename;

    public function getHtml( $post ) {

        $path = dirname( getcwd() ) . self::PARENT . $this->htmlFilename;

        require( $path );

    }
}

?>