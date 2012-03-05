<?php
require_once('Entity.php');
require_once('SlugService.php');

class EntityService {

	private $slug_service;

	private $logger;

	function __construct( &$slug_service) {
		$this->logger = Logger::getLogger(__CLASS__);

		$this->slug_service = $slug_service;
	}

	function unbind_all_entities_from_post(&$post_id) {
		$args = array(
			'numberposts' 	=> -1,
			'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
			'post_type'   	=> POST_CUSTOM_TYPE_ENTITY,
			'meta_key'		=> WORDLIFT_20_ENTITY_POSTS,
			'meta_value'	=> $post_id
		);

		$entity_posts = get_posts($args);

		foreach ($entity_posts as $entity_post) {
			delete_post_meta(	$entity_post->ID, 	WORDLIFT_20_ENTITY_POSTS, 	$post_id);	
		}
	}

	function bind_entity_to_post(&$entity_post_id, &$post_id) {
		delete_post_meta(	$entity_post_id, 	WORDLIFT_20_ENTITY_POSTS, 	$post_id);
		add_post_meta(		$entity_post_id,	WORDLIFT_20_ENTITY_POSTS, 	$post_id,	false);
	}

	function get_entities_by_post_id(&$post_id) {
		$args = array(
			'numberposts' 	=> -1,
			'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
			'post_type'   	=> POST_CUSTOM_TYPE_ENTITY,
			'meta_key'		=> WORDLIFT_20_ENTITY_POSTS,
			'meta_value'	=> $post_id
		);

		$posts = get_posts($args);

		return $this->create_entities_from_entity_posts( $posts );
		
		// $terms = get_the_terms( $post_id, WORDLIFT_20_TAXONOMY_NAME);

		// if (true == is_wp_error($terms)) {
		// 	$this->logger->error('Could not retrieve entities for post [id:'.$post_id.']: '.var_export($terms,true));
		// 	return null;
		// }

		// $slugs = array();
		// foreach ( $terms as $term ) {
		// 	$slugs[] = $term->slug;
		// }

		// $this->logger->debug('Found ['.count($slugs).'] slugs.');

		// return $this->create_entities_from_entity_posts( 
		// 			$this->get_entities_by_slugs( $slugs ) );
	}

	function create_entities_from_entity_posts( &$posts ) {
		$entities = array();

		foreach ($posts as $post) {
			$entities[] = $this->create_entity_from_entity_post( $post );
		}

		return $entities;
	}

	function create_entity_from_entity_post( &$post ) {

		$entity = new Entity();

		$post_meta 			= get_post_custom($post->ID);

		$entity->text 		= $post->post_title;
		$entity->slug 		= $post_meta[POST_META_ENTITY_SLUG][0];
		$entity->type 		= $post_meta[POST_META_ENTITY_TYPE][0];
		$entity->reference	= $post_meta[POST_META_ENTITY_ID][0];

		$entity->properties = array();

		foreach ($post_meta as $key => $values) {

			if (0 == strpos($key, POST_META_ENTITY_PREFIX) && $key != POST_META_ENTITY_SLUG && $key != POST_META_ENTITY_TYPE && $key != POST_META_ENTITY_ID) {
				$this_key = substr($key, strlen(POST_META_ENTITY_PREFIX));
				$entity->properties[] = array($this_key => $values);
			}
		}

		return $entity;
	}

	function get_entities_by_slugs( &$slugs ) {
		$args = array(
			'numberposts'	=> -1,
			'post_status' 	=> array('publish','pending','draft','auto-draft','future','private','inherit'),
			'post_type'   	=> POST_CUSTOM_TYPE_ENTITY,
			'meta_key' 	  	=> POST_META_ENTITY_SLUG,
			'meta_value'  	=> $slugs,
			'meta_compare' 	=> 'IN'
		);

		$posts = get_posts($args);

		$this->logger->debug('Found ['.count($posts).'] entities.');

		return $posts;
	}

	function get_entity_post_id( &$entity ) {
		$args = array(
				'numberposts' => 1,
				'post_status' => array('publish','pending','draft','auto-draft','future','private','inherit'),
				'post_type'   => POST_CUSTOM_TYPE_ENTITY,
				'meta_query'  => array(
					'relation' => 'AND',
						array(  'key' 	  => POST_META_ENTITY_TYPE,
								'value'   => $entity->type,
								'compare' => '='),
						array(  'key'	=> POST_META_ENTITY_ID,
								'value' => $entity->get_id(),
								'compare' => '=')
				)
			);

		$posts = get_posts($args);

		if (0 == count($posts)) return '';

		return $posts[0]->ID;
	}

	function get_parent_term( &$entity ) {

		if ('CreativeWork' === $entity->type)
			return WORDLIFT_20_TAXONOMY_CREATIVE_WORK;

		if ('Event' === $entity->type)
			return WORDLIFT_20_TAXONOMY_EVENT;

		if ('Organization' === $entity->type)
			return WORDLIFT_20_TAXONOMY_ORGANIZATION;

		if ('Person' === $entity->type)
			return WORDLIFT_20_TAXONOMY_PERSON;

		if ('Place' === $entity->type)
			return WORDLIFT_20_TAXONOMY_PLACE;

		if ('Product' === $entity->type)
			return WORDLIFT_20_TAXONOMY_PRODUCT;

		return WORDLIFT_20_TAXONOMY_OTHER;
	}

	function get_parent_id( &$entity ) {
		// $parent_term 	= $this->get_parent_term($entity->type);
		$parent_id 		= term_exists( $entity->type, WORDLIFT_20_TAXONOMY_NAME, 0);

		if (false == $parent_id) {
			$this->logger->warn('Could not find a term [term:'.$entity->type.'].');
			$parent_id 	= term_exists( WORDLIFT_20_TAXONOMY_OTHER, WORDLIFT_20_TAXONOMY_NAME, 0);
		}

		return $parent_id['term_id'];
	}

	function create( &$entity_array ) {

		$entity 			= new Entity();

		$entity->text 		= $entity_array->text;
		$entity->type 		= $entity_array->type;
		$entity->count 		= $entity_array->count;
		$entity->relevance 	= $entity_array->relevance;
		$entity->reference 	= $entity_array->reference;
		$entity->score     	= $entity_array->score;
		$entity->rank 		= $entity_array->rank;
		$entity->properties = $entity_array->properties;

		$entity->slug 		= $this->slug_service->get_slug($entity);

		return $entity;
	}

	function save( &$entity ) {

		$post_id 			= $this->get_entity_post_id($entity);
		$action				= (false != $post_id ? 'updated' : 'created');

		$wp_error 			= false;
		$post_id = wp_insert_post( array(
				'ID'   		 => $post_id,
				'post_title' => $entity->text,
				'post_type'  => POST_CUSTOM_TYPE_ENTITY
			),
			$wp_error
		);

		if (true == $wp_error) {
			$logger->error('An error occurred while creating a new post ['.var_export($wp_error,true).'].');
			return null;
		}

		add_post_meta($post_id, POST_META_ENTITY_TYPE, $entity->type, 		true);
		add_post_meta($post_id, POST_META_ENTITY_ID,   $entity->get_id(), 	true);
		add_post_meta($post_id, POST_META_ENTITY_SLUG, $entity->slug, 		true);

		$post_meta 	= get_post_custom($post_id);

		foreach ($entity->properties as $key => $values) {
			$meta_key = POST_META_ENTITY_PREFIX.$key;

			foreach ($values as $value) {
				if (null != $post_meta[$meta_key] && true == in_array( $value, array_values($post_meta[$meta_key]))) {
					// $this->logger->debug($meta_key.':'.$value.' already exists.');
				}
				else {
					add_post_meta($post_id, $meta_key, $value, false);
				}
			}
		}

		$term = term_exists($entity->get_term(), WORDLIFT_20_TAXONOMY_NAME, $this->get_parent_id($entity));

		$term_args = array(
				'slug'			=> $entity->slug,
				'parent'		=> $this->get_parent_id($entity)
			);
		if (false == $term) {
			$result = wp_insert_term( $entity->get_term(), WORDLIFT_20_TAXONOMY_NAME, 	$term_args);
		}
		else {
			// $result = wp_update_term( $term['term_id'], WORDLIFT_20_TAXONOMY_NAME, 		$term_args);	
		}

		// if ($result instanceof WP_Error && false == isset($result->errors->term_exists))
		//	$this->logger->error('The term ['.$entity->get_term().'] has not been created: '.var_export($result->errors, true));

		$this->logger->info('An entity with id [post_id:'.$post_id.'] has been '.$action.'.');
		return $post_id;

	}
}

$slug_service	= new SlugService();
$entity_service = new EntityService( $slug_service );

?>