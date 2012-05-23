<?php

/**
 * This class provide read/write access to the entities in this blog. 
 */
class EntityService {

	// The logger instance.
	private $logger;
	
	private $slugService;

	/**
	 * Initialize the class.
	 */
	function __construct($slugService) {
		$this->logger = Logger::getLogger(__CLASS__);
		
		$this->slugService = $slugService;
	}

	/**
	 * Adds the entity ID to the post ID, and sets the entity as accepted for that post.
	 * @param integer $entity_post_id
	 * @param integer $post_id
	 * @return NULL
	 */
	public function bind_entity_to_post($entity_post_id, $post_id) {
		delete_post_meta(	$entity_post_id, 	WORDLIFT_20_ENTITY_POSTS, 	$post_id);
		add_post_meta(		$entity_post_id,	WORDLIFT_20_ENTITY_POSTS, 	$post_id,	false);
	}

	/**
	 * Marks an entity as accepted for the specified post.
	 * @param integer $entity_post_id
	 * @param integer $post_id
	 */
	function accept_entity_for_post($entity_post_id, $post_id) {
		delete_post_meta(	$entity_post_id, 	WORDLIFT_20_ENTITY_REJECTED, 	$post_id);
		delete_post_meta(	$entity_post_id, 	WORDLIFT_20_ENTITY_ACCEPTED, 	$post_id);
		add_post_meta(		$entity_post_id,	WORDLIFT_20_ENTITY_ACCEPTED, 	$post_id,	false);
	}
	
	function setEntityBogus($entity_post_id, $bogus) {
		delete_post_meta(	$entity_post_id, 	WORDLIFT_20_ENTITY_BOGUS, 	$bogus);
		add_post_meta(		$entity_post_id,	WORDLIFT_20_ENTITY_BOGUS, 	$bogus,	false);
	}
	
	/**
	 * Retrieves all the entities in this blog.
	 * @param integer $limit
	 * @param integer $offset
	 * @return multitype:Entity
	 */
	function get_all($limit, $offset) {
		$args = array(
				'numberposts' 	=> $limit,
				'offset' 		=> $offset,
				'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
				'post_type'   	=> WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				'orderby'  		=> 'title',
				'order' 		=> 'ASC'
		);
	
		$entity_posts 		= get_posts($args);
	
		return $this->create_entities_from_entity_posts( $entity_posts );
	}
	
	/**
	 * Returns a structure with the total number of entities:
	 *  {}->publish
	 *  {}->draft
	 * @return Array('publish','draft')
	 */
	function get_count() {
		$counts = wp_count_posts(WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE);
		$total  = 0;
		foreach ($counts as $key => $value)
			$total += $value;
	
		return $total;
	}

	/**
	 * Gets a list of entities whose names contain the specified value.
	 * @param string $name
	 * @param integer $limit
	 * @param integer $offset
	 * @return multitype:Entity
	 */
	public function findEntitiesByName($name, $limit = -1, $offset = 0) {
		
		$args = array(
				'numberposts' 	=> $limit,
				'offset' 		=> $offset,
				'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
				'post_type'   	=> WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				'meta_query'	=> array(
					array(
						'key'		=> WORDLIFT_20_FIELD_NAME,
						'value'		=> $name,
						'compare'	=> 'LIKE'
					)
				),				
				'orderby'  		=> 'title',
				'order' 		=> 'ASC'
		);
		
		$entity_posts 		= get_posts($args);
		
		return $this->create_entities_from_entity_posts( $entity_posts );
	}
	
	/**
	 * Saves the data for an entity by looking for properties that starts with the WordLift 2.0 prefix.
	 * @param integer $post_id
	 */
	function save_entity_from_post_edit($post_id) {
		$this->logger->debug('Saving post [post_id:'.$post_id.'].');

		if (WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE != get_post_type( $post_id )) return;

		$keys = get_post_custom_keys($post_id);

		// delete existing values.
		foreach ($keys as $key) {
			if (0 == strpos( $key, WORDLIFT_20_FIELD_PREFIX )) {
				delete_post_meta($post_id, $key);
			}
		}

		// save the new values.
		foreach ($_POST as $key => $value) {
			if (0 == strpos( $key, WORDLIFT_20_FIELD_PREFIX )) {
				add_post_meta($post_id, $key, $value);
			}
		}

	}

	


	function unbind_all_entities_from_post(&$post_id) {
		$args = array(
				'numberposts' 	=> -1,
				'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
				'post_type'   	=> WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				'meta_key'		=> WORDLIFT_20_ENTITY_POSTS,
				'meta_value'	=> $post_id
		);

		$entity_posts = get_posts($args);

		foreach ($entity_posts as $entity_post) {
			delete_post_meta(	$entity_post->ID, 	WORDLIFT_20_ENTITY_POSTS, 	$post_id);
		}
	}

	function reject_entity_for_post(&$entity_post_id, &$post_id) {


		delete_post_meta(	$entity_post_id, 	WORDLIFT_20_ENTITY_REJECTED, 	$post_id);
		delete_post_meta(	$entity_post_id, 	WORDLIFT_20_ENTITY_ACCEPTED, 	$post_id);
		add_post_meta(		$entity_post_id,	WORDLIFT_20_ENTITY_REJECTED, 	$post_id,	false);

		// 		$this->logger->debug('Entity ['.$entity_post_id.'] will be rejected for post ['.$post_id.']: '.$result.'.');
	}

	function get_accepted_entities_by_post_id(&$post_id) {
		$args = array(
				'numberposts' 	=> -1,
				'post_status' 	=> "publish, pending, draft, auto-draft, future, private, inherit", // array(),
				'post_type'   	=> WordLiftPlugin::POST_TYPE,
				'meta_key'  	=> WordLiftPlugin::ACCEPTED_POSTS,
				'meta_value'	=> $post_id,
				'meta_compare'  => 'IN'
		);

		$entity_posts = get_posts($args);

		return $this->create_entities_from_entity_posts( $entity_posts, $post_id );
	}

	function get_all_accepted_entities() {
		$args = array(
				'numberposts' 	=> -1,
				'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
				'post_type'   	=> WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				'meta_key'  	=> WORDLIFT_20_ENTITY_ACCEPTED,
				'meta_value'	=> NULL,
				'meta_compare'  => '<>'
		);

		$entity_posts = get_posts($args);

		return $this->create_entities_from_entity_posts( $entity_posts, $post_id );
	}
	
	function get_entities_by_post_id(&$post_id) {
		$args = array(
				'numberposts' 	=> -1,
				'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
				'post_type'   	=> WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				'meta_query'  => array(
						'relation' => 'OR',
						array(  'key' 	  	=> WORDLIFT_20_ENTITY_POSTS,
								'value'   	=> $post_id,
								'compare' 	=> 'IN')
						, array(  'key'		=> WORDLIFT_20_ENTITY_ACCEPTED,
								'value' 	=> $post_id,
								'compare' 	=> 'IN')
						, array(  'key'		=> WORDLIFT_20_ENTITY_REJECTED,
								'value' 	=> $post_id,
								'compare' 	=> 'IN')
				)
				// 'meta_key'		=> WORDLIFT_20_ENTITY_POSTS,
				// 'meta_value'	=> $post_id
		);

		$entity_posts = get_posts($args);

		return $this->create_entities_from_entity_posts( $entity_posts, $post_id );
	}

	function create_entities_from_entity_posts( &$entity_posts, $post_id = NULL) {
		$entities = array();

		foreach ($entity_posts as $entity_post) {
			$entities[] = $this->create_entity_from_entity_post( $entity_post, $post_id );
		}

		return $entities;
	}

	function create_entity_from_entity_post( &$entity_post, $post_id ) {

		$entity = new Entity();

		$post_meta 			= get_post_custom($entity_post->ID);

		$entity->text 		= $entity_post->post_title;
		$entity->type 		= $post_meta[WORDLIFT_20_FIELD_SCHEMA_TYPE][0];
		$entity->about		= $post_meta[WORDLIFT_20_FIELD_ABOUT][0];
		$entity->setPostId($entity_post->ID);

		$entity->setPosts( $post_meta[WORDLIFT_20_ENTITY_POSTS] );
		$entity->setAcceptedPosts( $post_meta[WORDLIFT_20_ENTITY_ACCEPTED] );
		$entity->setRejectedPosts( $post_meta[WORDLIFT_20_ENTITY_REJECTED] );
		$entity->setBogus( (true == $post_meta[WORDLIFT_20_ENTITY_BOGUS] ? true : false) );

		if (NULL != $entity->getAcceptedPosts())
			$entity->accepted = in_array( $post_id, $entity->getAcceptedPosts());

		if (NULL != $entity->getRejectedPosts())
			$entity->rejected = in_array( $post_id, $entity->getRejectedPosts());

		$entity->properties = array();

		// add the custom fields as properties of the entity.
		foreach ($post_meta as $key => $values) {
			if (0 == strpos($key, WORDLIFT_20_FIELD_PREFIX)) {
				$this_key = substr($key, strlen(WORDLIFT_20_FIELD_PREFIX));
				$entity->properties[$this_key] = $values;
			}
		}

		return $entity;
	}

	function get_entity_post_id( &$entity ) {
		$args = array(
				'numberposts' 	=> 1,
				'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
				'post_type'   	=> WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				'meta_key'  	=> WORDLIFT_20_FIELD_ABOUT,
				'meta_value' 	=> $entity->get_id()
		);

		$posts = get_posts($args);

		if (0 == count($posts)) return '';

		return $posts[0]->ID;
	}

	function create( &$entity_array ) {
	    $this->logger->debug("Creating a new entity.");

		$entity 			= new Entity();

		$entity->text 		= $entity_array->text;
		$entity->type 		= $entity_array->type;
		$entity->count 		= $entity_array->count;
		$entity->relevance 	= $entity_array->relevance;
		$entity->about 		= $entity_array->reference;
		$entity->score     	= $entity_array->score;
		$entity->rank 		= $entity_array->rank;
		$entity->properties = $entity_array->properties;

	    if (null === $this->slugService) {
	        $this->logger->warn("The slugService is not set.");
	    } else {
		    $entity->slug 		= $this->slugService->get_slug($entity);
        }
    
		return $entity;
	}

	function add_properties(&$entity,$post_id) {

		delete_post_meta($post_id, WORDLIFT_20_FIELD_PREFIX.'description');
		delete_post_meta($post_id, WORDLIFT_20_FIELD_PREFIX.'name');
		delete_post_meta($post_id, WORDLIFT_20_FIELD_PREFIX.'url');
		delete_post_meta($post_id, WORDLIFT_20_FIELD_PREFIX.'image');
		delete_post_meta($post_id, WORDLIFT_20_FIELD_SCHEMA_TYPE);
		delete_post_meta($post_id, WORDLIFT_20_FIELD_LATITUDE);
		delete_post_meta($post_id, WORDLIFT_20_FIELD_LONGITUDE);

		add_post_meta($post_id,WORDLIFT_20_FIELD_PREFIX.'schema-type',$entity->type,true);

		// description
		if (array_key_exists('description', $entity->properties))
			add_post_meta($post_id,WORDLIFT_20_FIELD_PREFIX.'description',$entity->properties->description[0],true);
		// name
		if (array_key_exists('label', $entity->properties))
			add_post_meta($post_id,WORDLIFT_20_FIELD_PREFIX.'name',$entity->properties->label[0],true);
		// url
		add_post_meta($post_id,WORDLIFT_20_FIELD_PREFIX.'url',$entity->get_id(),true);
		// image
		if (array_key_exists('thumbnail', $entity->properties))
			add_post_meta($post_id,WORDLIFT_20_FIELD_PREFIX.'image',$entity->properties->thumbnail[0],true);

		if (array_key_exists('latitude', $entity->properties) && array_key_exists('longitude', $entity->properties)) {
			add_post_meta($post_id,WORDLIFT_20_FIELD_LATITUDE,$entity->properties->latitude[0],true);
			add_post_meta($post_id,WORDLIFT_20_FIELD_LONGITUDE,$entity->properties->longitude[0],true);
		}
	}

	function save( &$entity ) {
	    $this->logger->debug("Saving an entity.");

		$post_id 			= $this->get_entity_post_id($entity);
		$action				= (false != $post_id ? 'updated' : 'created');

		$wp_error 			= false;
		$post_id = wp_insert_post( array(
				'ID'   		 => $post_id,
				'post_title' => $entity->text,
				'post_type'  => WORDLIFT_20_ENTITY_CUSTOM_POST_TYPE,
				'post_status' => 'publish'),
				$wp_error
		);

		if (true == $wp_error) {
			$this->logger->error('An error occurred while creating a new post ['.var_export($wp_error,true).'].');
			return null;
		}

		$this->add_properties($entity, $post_id);

		$this->logger->info('An entity with id [post_id:'.$post_id.'] has been '.$action.'.');

		return $post_id;

	}
}

$entity_service = new EntityService(
        new SlugService()
    );
?>