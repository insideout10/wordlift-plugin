<?php
/**
 * User: david
 * Date: 15/07/12 16:47
 */

class WordLift_EntityPostType implements WordLift_IEntityPostType {

    public $logger;

    public $rewriteSlug;
    public $customPostType;
    public $menuIconURL;

    /**
     * Registers the meta-box handler.
     */
    private function register_meta_box_cb(){
        add_meta_box('entities-properties','Properties', array( $this, 'entities_properties_box'), $this->customPostType);
    }

    /**
     * Draws the meta-box.
     */
    private function entities_properties_box( $post ){

        $custom_fields 	= get_post_custom($post->ID);
    }

    public function getArguments() {
        $this->logger->trace( "Will use [$this->rewriteSlug] as rewrite slug." );

        return array(
            'labels' => array(
                'name' 			=> _x('Entities', ''),
                'singular_name' => _x('Entity', ''),
                'search_items'  => _x('Search Entities', ''),
                'popular_items' => _x('Popular Entities', ''),
                'all_items' 	=> _x('All Entities', ''),
                'parent_item'   => _x('Parent', ''),
                'parent_item_colon' => _x('Parent:', ''),
                'edit_item'		=> _x('Edit Entity', ''),
                'update_item'	=> _x('Update Entity', ''),
                'add_new_item'  => _x('Add New Entity', ''),
                'new_item_name' => _x('New Entity Name', ''),
                'separate_items_with_commas' => _x('Separate Entities with Commas', ''),
                'add_or_remove_items' => _x('Add or Remove Entities', ''),
                'choose_from_most_used' => _x('Choose from the most used Entities', ''),
                'menu_name'		=> _x('Entities', '')
            ),
            'exclude_from_search'   => false,
            'publicly_queryable'	=> true,
            'description' 			=> 'The entities found in this blog.',
            'public' 				=> true,
            'has_archive' 			=> true,
            'menu_icon'				=> plugins_url($this->menuIconURL),
            'menu_position' 		=> 21,
            'supports' 				=> array('title'),
//            'register_meta_box_cb' 	=> array( $this, 'register_meta_box_cb'),
            'rewrite' 				=> array( 'slug' => $this->rewriteSlug ),
            'taxonomies'			=> array(
                'category',
                'post_tag'
            )
        );
    }

}

?>