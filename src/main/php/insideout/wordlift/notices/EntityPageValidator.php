<?php

class WordLift_Notices_EntityPageValidator
{

    public $optionName;
    public $logger;
    public $shortCode;

    public function validate()
    {
        $pageId = get_option($this->optionName, null);

        if (null === $pageId) {
            $pageId = $this->createEntityPage();
            update_option($this->optionName, $pageId);
        }

        return "";
    }

    private function createEntityPage()
    {
        $pageId = wp_insert_post(
            array(
                "post_content" => "[$this->shortCode]",
                "post_status" => "publish",
                "post_title" => "",
                "post_type" => "page"
            ),
            $error
        );

        if (is_wp_error($error)) {
            $this->logger->error("An error occurred creating the entity page.");
        }

        return $pageId;
    }

}

?>