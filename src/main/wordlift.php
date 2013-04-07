<?php
/*
Plugin Name: WordLift
Plugin URI: http://wordlift.it
Description: Supercharge your WordPress Site with Smart Tagging and #Schemaorg support - a brand new way to write, organise and publish your contents to the Linked Data Cloud.
Version: {version}
Author: InSideOut10
Author URI: http://www.insideout.io
License: APL
*/

// bootstrap the WordPress framework.
require_once("php/insideout/wordpress/bootstrap.php");

WordPress_XmlApplication::setUp(
    dirname(__FILE__),
    "/wordlift.xml",
    "/log4php.xml"
);

function wordlift_activate()
{
    // delete_option("wordlift_consumer_key");
    // delete_option("wordlift_site_key");
}

function wordlift_deactivate()
{
    delete_option("wordlift_consumer_key");
    delete_option("wordlift_site_key");
}

function wordlift_scripts() {
    wp_enqueue_script(
        "wordlift-bar",
        plugins_url("wordlift/js/wordlift-bar.js"),
        array("jquery")
    );
}

function wordlift_footer() {
    if ("true" !== get_option("wordlift_show_footer_bar", "true")) {
        return;
    }

    $args = array(
        "numberposts" => 1,
        "orderby" => "post_date",
        "order" => "DESC",
        "post_type" => "post",
        "post_status" => "publish",
        "suppress_filters" => true
    );

    $recentPosts = wp_get_recent_posts($args, $output = ARRAY_A);

    if (0 === count($recentPosts)) {
        return;
    }
    
    $id = $recentPosts[0]["ID"];

    $context = WordPress_XmlApplication::getContext("wordLift");
    $postEntitiesService = $context->getClass("postEntitiesService");
    $entities = $postEntitiesService->get($id);

    $entitiesCount = count($entities);
    if (0 === $entitiesCount) {
        return;
    }

    $index = 0;
    $languages = $postEntitiesService->getPostLanguages($id);

    echo("<div id=\"wordlift-bar\">");
    echo("<ul>");
    echo("<li class=\"separator\"></li>");
    foreach ($entities as $key => &$entity) {
        $index++;

        $link = admin_url(
            "admin-ajax.php?action=wordlift.gotoentity&e=" . urlencode($key)
        );

        $type = $postEntitiesService->getFirstValue($entity, "type");
        $shortType = strtolower(substr($type, strrpos( $type, "/") + 1));

        $name = $postEntitiesService->getValueByLanguage(
            $entity,
            "name",
            $languages
        );
        $name = htmlspecialchars(
            $name,
            ENT_COMPAT,
            "UTF-8"
        );

        if (empty($name)) {
            continue;
        }
        
        $title = $postEntitiesService->getValueByLanguage(
            $entity,
            "title",
            $languages
        );

        $image = $postEntitiesService->getFirstValue(
            $entity,
            "image"
        );
        $description = $postEntitiesService->getValueByLanguage(
            $entity,
            "description",
            $languages
        );
        $description = htmlspecialchars(
            $description,
            ENT_COMPAT,
            "UTF-8"
        );

        echo("<li itemscope itemtype=\"$type\" class=\"entity $shortType\">");
        echo("<a href=\"$link\">");
        echo("<h1 itemprop=\"name\">$name</h1>\n");
        echo("<h2 itemprop=\"title\">$title<h2>\n");
        echo("<img onerror=\"this.parentNode.removeChild(this);\" itemprop=\"image\" src=\"$image\" />\n");
        echo("<p itemprop=\"description\">$description</p>\n");
        echo("</a>");
        echo("</li>");
        echo("<li class=\"separator\"></li>");
    }
    echo("</ul>");
    echo("<div id=\"wordlift-bar-switch\">");
    echo("</div>");
    echo("</div>");
}

register_activation_hook(__FILE__, "wordlift_activate");
register_deactivation_hook(__FILE__, "wordlift_deactivate");
add_action("wp_enqueue_scripts", "wordlift_scripts");
add_action("wp_footer", "wordlift_footer");
?>
