<?php
/**
 * User: david
 * Date: 15/07/12 16:47
 */

class WordLift_EntityPostType implements WordPress_IPostType {

    public $logger;

    public $rewriteSlug;
    public $customPostType;
    public $menuIconURL;

    public $fieldPrefix;
    public $fieldType;

    /**
     * Registers the meta-box handler.
     */
    public function registerMetaBox(){
        $this->logger->trace( "Registering meta-box for type [$this->customPostType]." );
//        add_meta_box('entities-properties','Properties', array( $this, 'entities_properties_box'), $this->customPostType);
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

    /**
     * Must conform to http://codex.wordpress.org/Plugin_API/Action_Reference/manage_$post_type_posts_custom_column
     * @param $column
     * @param $postID
     * @return mixed
     */
    public function getColumnValue( $column, $postID ) {
        if ( $this->customPostType !== get_post_type( $postID ) )
            return;

        echo get_post_meta( $postID, $column, true );
    }

    /**
     * Must conform to http://codex.wordpress.org/Plugin_API/Filter_Reference/manage_edit-post_type_columns
     * @param $columns
     * @return array
     */
    public function getColumns( $columns ) {
        if ( true === array_key_exists( "categories", $columns ) )
            unset( $columns["categories"] );

        return array_merge($columns, array(
            $this->fieldPrefix . "name" => __("Name"),
            $this->fieldType => __("Type")
        ) );
    }

}

?>