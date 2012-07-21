<?php

class WordPress_AjaxService implements WordPress_IAjaxService {

    const WP_AJAX_NOPRIV = "wp_ajax_nopriv_";
    const WP_AJAX = "wp_ajax_";
    const INVOKE = "invoke";

    public $logger;

    public $jsonService;

    public function bindAction( $instance, $method, $action, $authentication = false, $capabilities = "any", $compression = true ) {

        $this->logger->trace( "Binding $action to method $method [authentication :: $authentication][capabilities :: $capabilities][compression :: $compression]." );

        $proxy = new WordPress_AjaxProxy( $instance, $method, $action, $authentication, $capabilities, $compression, $this->jsonService );


        // enable public access to the ajax end-point.
        if ( !$authentication ) {
            // bind the action to the function.
             do_action(self::WP_AJAX_NOPRIV . $action);
             add_action(self::WP_AJAX_NOPRIV . $action, array( $proxy, self::INVOKE ) );
        }

        // enable protected access to the ajax end-point.
        do_action(self::WP_AJAX . $action);
        add_action(self::WP_AJAX . $action, array( $proxy, self::INVOKE ) );

    }
}

?>