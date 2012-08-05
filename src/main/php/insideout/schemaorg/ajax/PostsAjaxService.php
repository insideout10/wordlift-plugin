<?php

class SchemaOrg_PostsAjaxService {

	public $logger;

    public $preprocessor;
    public $saveToUploads;
    public $proxyService;
    public $entityPostType;
    public $entityPostStatus;
    public $entityFieldPrefix;
    public $entityFieldType;
    public $schemaOrgFieldType;

    /**
     * @service ajax
     * @action schema-org.posts
     * @authentication none
     */
    public function getPosts($schema = null, $name = null, $url = null) {

        $wordPressRepository = new WordPressRepository( WordLiftPlugin::POST_TYPE, WordLiftPlugin::FIELD_PREFIX . "schema-type", '', WordLiftPlugin::FIELD_PREFIX);

        if (null !== $schema && null !== $name) {
        	$items = $wordPressRepository->findBySchemaAndNameLike($schema, $name);
        } else if (null !== $schema) {
        	$items = $wordPressRepository->findBySchema($schema);
        } else if (null !== $url) {
        	$items = $wordPressRepository->findByUrl($url);
        } else {
        	$items = $wordPressRepository->findAll();
        }

        // clean out null properties
        // $items = array_filter(get_object_vars($items));
        array_walk_recursive($items, create_function(
              '&$array',
              '$type = get_class($array);' .
        	    '$array = array_filter((array)$array);' .
        	    '$array[\'@type\'] = $type;'
        ));
        
        return $items;
    }
    
    /**
     * @service ajax
     * @action schema-org.create-post
     * @authentication none
     * @requireCapabilities publish_posts
     */
    public function createPost($requestBody = null) {

        $properties = json_decode($requestBody, true);

        // save the json file locally.
        if ( "true" === $this->saveToUploads )
            $this->saveInboundFileToUploads( $requestBody );

        $return = $this->create( $properties );

        $this->logger->trace( "A post has been created with the following result: [$return]." );

        return WordPress_AjaxProxy::CALLBACK_RETURN_NULL;
    }

    private function saveInboundFileToUploads( $requestBody ) {
        $uploadDirectory = wp_upload_dir();
        $localFileName = $uploadDirectory[ "path" ] . "/" . date("YmdHis.u") . ".json";

        $this->logger->trace( "A JSON file will be saved to [$localFileName]." );
        file_put_contents( $localFileName, $requestBody);
    }
    
    private function create( $properties ) {

        if ( 0 < count( $properties ) && is_array( reset( $properties ) ) ) {
            $value = array();
            foreach ($properties as $property) {
                # create nested posts.
                array_push( $value, $this->create( $property ));
            }
            return $value;
        }

        if ( array_key_exists( "@type", $properties )
                && NULL !== $this->preprocessor
                && $this->preprocessor->supportsType( $properties["@type"]) ) {

            $this->preprocessor->process( $properties );
        } else {
            $this->logger->warn("The @type property is not specified for this entity.");
        }

        $post = array(
            "post_type" => $this->entityPostType,
            "post_status" => $this->entityPostStatus
        );
        
        $post_meta = array();
        
        # check if we have an ID property.
        if ( array_key_exists( "@id", $properties ) && null !== $properties["@id"]) {
            $atId = $properties["@id"];
            
            # check if an existing post exists.
            $args = array(
                "numberposts" => -1,
                "offset" => 0,
                "orderby" => "post_date",
                "order" => "DESC",
                "meta_key" => $this->entityFieldPrefix . "@id",
                "meta_value" => $atId,
                "post_type" => $this->entityPostType,
                "post_status" => "any"
            );
            
            $posts = get_posts($args);
            
            if (1 === count($posts)) {
                $post = get_object_vars($posts[0]);
                $postId = $post["ID"];

                $this->logger->debug("Found an existing post with [@id:$atId][postId:$postId], incoming data is going to be used to update the post.");

                $post_meta = get_post_meta($postId, "");
            }
        }
        
        // exit;
        
        foreach ($properties as $key => $value) {
            # avoid any issue with case-sensitivity.
            $key = strtolower($key);

            # set the meta key used for storing values in WordPress custom fields.
            $metaKey = $this->entityFieldPrefix . $key;


            # TODO: need to support some extensions here:
            #  1] to intercept the 'genre' field and set the categories accordingly.
            #  2] to intercept the 'ID' field and see if another instance already exists
            #      with the same ID.
            #  3] to intercept the 'keywords' and treat them as tags. 

            if ( "thumbnailurl" === $key ) {
                $value = $this->proxyService->cacheURL( $value );
            }

            # save the keywords as post tags.
            if ("keywords" === $key) {
                $post["tax_input"] = array("post_tag" => explode(",", $value));
            }

            # save the genre as categories.
            if ("genre" === $key) {
                $bindTo = array();
                
                $this->logger->trace("Must bind this item to the category [$value].");
                
                # example: Reti e Impianti/Produzione; Innovazione/Progetti; Innovazione/Tecnologie
                $paths = explode(";", $value);
                
                
                foreach ($paths as $path) {
                    $path = trim($path);
                    $names = explode("/", $path);

                    $parentCategory = '';
                    
                    foreach ($names as $name) {
                        $name = trim($name);
                        
                        $args = array(
                        	'type'                     => 'post',
                        	'child_of'                 => 0,
                        	'parent'                   => $parentCategory,
                        	'orderby'                  => 'name',
                        	'order'                    => 'ASC',
                        	'hide_empty'               => 0,
                        	'hierarchical'             => 0,
                        	'exclude'                  => '',
                        	'include'                  => '',
                        	'number'                   => '',
                        	'taxonomy'                 => 'category',
                        	'pad_counts'               => false );

                        $categories = get_categories($args);
                        
                        $found = Array();
                        foreach ($categories as $category) {
                            if ($name === $category->name)
                                $found[] = $category->cat_ID;
                        }

                        $parentCategory = implode(",", $found);
                    }
                    
                    $bindTo[$path] = $parentCategory;
                }
                
                # set the category IDs for the post.
                $post["post_category"] = array_values($bindTo);

            }

            // set the schema-type.
            if ( $this->schemaOrgFieldType === $key) {
                $post_meta[ $this->entityFieldType ] = $value;
                continue;
            }

            // create depedencies if the value is an array of properties.
            if (true === is_array($value)) {
                # initialize the property if it's not initialized already.
                if ( !array_key_exists( $metaKey, $post_meta ) || !is_array($post_meta[$metaKey]))
                    $post_meta[$metaKey] = array();

                # create the dependencies.
                $dependencies = $this->create( $value );

                # merge the dependencies into the array or push the dependency into the property array.
                if (true === is_array($dependencies)) {
                    $post_meta[$metaKey] = array_merge( $post_meta[$metaKey], $dependencies );
                } else {
                    array_push( $post_meta[$metaKey], $dependencies );
                }

                continue;
            }

            // set the name.
            if ( "name" === $key) {
                $post[ "post_title" ] = $value;
                // we want to save this property also in the custom fields, we don't "continue".
            }

            // set this post custom-fields.
            $post_meta[ $this->entityFieldPrefix . $key] = $value;
        }
        
        if ( !array_key_exists( "post_title", $post ) || null === $post[ "post_title" ])
            $post[ "post_title" ] = uniqid("", true);

        # see http://codex.wordpress.org/Function_Reference/wp_insert_post
        $post_id = wp_insert_post($post, true);
        if ( is_wp_error($post_id) ) {
            
            $errorMessage = $post_id->get_error_message();
            $this->logger->error("an error occurred saving a post: $errorMessage.");

            return NULL;
        }

        $this->logger->debug("post saved with id [$post_id].");

        # save the meta-data.
        foreach ($post_meta as $key => $values) {

            # save each value.
            if (true === is_array($values)) {

                foreach ($values as $value) {
                    add_post_meta($post_id, $key, $value);
                }

            } else {
                add_post_meta($post_id, $key, $values);
            }
        }

        # get the saved post to catch the slug/post_name.
        $post = get_post($post_id);
        $postName = $post->post_name;

        return $postName;

    }
    
}

?>