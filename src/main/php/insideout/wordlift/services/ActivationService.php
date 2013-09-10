<?php

class WordLift_ActivationService
{

    public $logger;
    public $apiUrl;
    public $menuUrl;

    public $operations;

    public function activate()
    {

        $this->logger->trace( "Activating the WordLift Plugin..." );

        $siteKey = $this->getSiteKey();
        
        // add the key locally if the site is registered already.
        if (null !== $siteKey) {
            add_option("wordlift_site_key", $siteKey);

            echo("<div class=\"updated\"><p>");
            echo("<strong>Information</strong>: the WordLift site key has"
                . " been set successfully.");
            echo("</p></div>");

            // create a phantom page for the entity page.
            $this->create_page();

            return;
        }

        // request a site key.
        $operations = WordLift_HttpOperations::create(
            $this->apiUrl,
            WordLift_HttpOperations::CONTENT_TYPE_JSON,
            WordLift_HttpOperations::CONTENT_TYPE_JSON
        );

        $response = $operations->post(
            null,
            json_encode(
                array(
                    "url" => $this->getUrl()
                )
            )
        );

        if (is_wp_error($response)) {
            $errorMessage = $response->get_error_message();
            echo("<div class=\"error\"><p>");
            echo("<strong>Error</strong>: WordLift could not set or get a"
                . " valid site key ($errorMessage while connecting to "
                . "$this->apiUrl).");
            echo("</p></div>");

            return;
        }

        $respObject = json_decode($response["body"]);
        if (property_exists($respObject, "key")) {
            add_option("wordlift_site_key", $respObject->key);

            echo("<div class=\"updated\"><p>");
            echo("<strong>Information</strong>: the WordLift site key has"
                . " been set successfully.");
            echo("</p></div>");

            // create a phantom page for the entity page.
            $this->create_page();

            return;
        }


        echo("<div class=\"error\"><p>");
        echo("<strong>Error</strong>: WordLift could not set or get a valid"
                . " site key.");
        echo("</p></div>");
    }

    private function getUrl()
    {
        return admin_url($this->menuUrl);
    }

    private function getSiteKey()
    {
        $operations = WordLift_HttpOperations::create(
            $this->apiUrl,
            WordLift_HttpOperations::CONTENT_TYPE_JSON,
            WordLift_HttpOperations::CONTENT_TYPE_JSON
        );

        $response = $operations->get(
            array(
                "url" => $this->getUrl()
            )
        );

        if (is_wp_error($response)) {
            $errorMessage = $response->get_error_message();
            echo("<div class=\"error\"><p>");
            echo("<strong>Error</strong>: WordLift could not set or get a"
                . " valid site key ($errorMessage while connecting to "
                . "$this->apiUrl).");
            echo("</p></div>");

            return null;
        }

        $respObject = json_decode($response["body"]);
        if (property_exists($respObject, "key")) {
            return $respObject->key;
        } 

        return null;
    }

    private function create_page()
    {

        $page = array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_name'    => 'wordlift-entity',
            'post_content' => '[wordlift.entity]',
            'post_title'   => 'WordLift Entity'
        );

        $error = null;
        $page_id = wp_insert_post( $page, $error );

        // add the entity page option and remove the page from menus.
        if ( is_numeric( $page_id ) && 0 !== $page_id ) {

            update_option( '_wordlift_entity_page_id', $page_id );

            $args = array(
                'meta_key'   => '_menu_item_object_id',
                'meta_value' => $page_id,
                'post_type'  => 'nav_menu_item'
            );

            $menu_items = get_posts( $args );

            foreach ( $menu_items as $menu_item ) {
                if ( is_nav_menu_item( $menu_item->ID ) ) {
                    wp_delete_post( $menu_item->ID, true );
                }
            }
            
        }

        $this->logger->info(var_export($error, true));
    }

}

?>