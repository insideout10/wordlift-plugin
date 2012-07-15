<?php

/**
 * Provides access to WordPress posts.
 */
class PostService {
	
	
	/**
	 * Find all the posts belonging to a category specified by its slug.
	 * @param string $slug The category slug.
	 * @param integer $offset The offset from where to start (default = 0).
	 * @param integer $limit The maximum number of results (default = unlimited).
	 * @return array An array of posts.
	 */
	public function findByCategorySlug($slug, $types = array('any'), $includeSubcategories = false, $offset = 0, $limit = -1) {
		$category = get_category_by_slug($slug);

		$args = $this->getDefaultArgs($types, $offset, $limit);
		$args['orderby'] = 'date DESC';
		
		if (true == $includeSubcategories) {
			$args = array_merge($args, array('cat' => $category->cat_ID ));
		} else {
			$args = array_merge($args, array('category__in' => array( $category->cat_ID )));
		}
		
		$query = new WP_Query( $args );
		
		return array(
					'offset' =>	$query->get('offset'),
					'count' => $query->post_count,
					'total' => $query->found_posts,
					'next' => ($query->get('offset') + $query->post_count < $query->found_posts),
					'previous' => ($query->get('offset') > 0),
					'posts' => $query->posts
				);
	}
	
	/**
	 * Find all the posts that have the specified slug names.
	 * @param array $slugs An array of slugs (or post-names).
	 * @return array An array of posts objects.
	 */
	public function findBySlugNames(&$slugs) {
		if (false == is_array($slugs) || 0 == sizeof($slugs))
			return array();
		
		global $wpdb;
		
		$query = "SELECT * FROM $wpdb->posts WHERE post_name = %s";
		
		if (1 < sizeof($slugs)) {
			for ($i = 1; $i < sizeof($slugs); $i++) {
				$query .= ' OR post_name = %s'; 
			}
		}

		$posts = $wpdb->get_results( $wpdb->prepare(
					$query,
					$slugs
				), OBJECT);
		
		return $posts;
	}

	public function findRelated($postId, $types = array('any'), $offset = 0, $limit = -1) {
		
		$tagIDs = array();
        $tags = get_the_tags($postId);
        
        if (false === $tags)
            return array();

		foreach (get_the_tags($postId) as $tag)
			$tagIDs[] = $tag->term_id;
		
		return $this->findByTags($tagIDs, $types, $offset, $limit, array($postId));
	}
	
	public function findByTags(&$tags, $types = array('any'), $offset = 0, $limit = -1, $excludePosts = null) {

		$args = $this->getDefaultArgs($types, $offset, $limit, $excludePosts);
		
		if (is_array($tags))
			$tagsArray = $tags;
		
		if (is_object($tags))
			$tagsArray = get_object_vars($tags);

		$args = array_merge(
					$args,
					array(
						'tag__in' => $tagsArray
					)
				);
		
		return get_posts($args);
	}
	
	public function findAll($customArgs = null, $types = array('any'), $offset = 0, $limit = -1, $excludePosts = null) {
	
		$args = $this->getDefaultArgs($types, $offset, $limit, $excludePosts);
		
		if (null != $customArgs) {
			$args = array_merge(
				$args,
				$customArgs
			);
		}
		
		return get_posts($args);
	}
	
	/**
	 * Returns the default set of arguments for the get_posts call.
	 * @param array $ypes An array of post types. Default 'any'. 
	 * @param integer $offset The offset from where to start (default = 0).
	 * @param integer $limit The maximum number of results (default = unlimited).
	 */
	private function getDefaultArgs($types = array('any'), $offset = 0, $limit = -1, $excludePosts = null) {
		$args = array(
				'numberposts' => $limit,
				'posts_per_page' => $limit,
				'offset' => $offset,
				'post_type' => $types,
				'post_status' => 'any',
				'orderby' => 'rand'
		);
		
		if (true == is_array($excludePosts)) {
			$args = array_merge(
						$args,
						array(
							'post__not_in' => $excludePosts
						)
					);
		}
		
		return $args;
	}
	
	/**
	 * The posts array (resulting from a get_posts call) don't have the custom_fields. This method 
	 * will add the custom fields in the 'custom' key to every post in the array and return the array itself.
	 * @param array $posts
	 * @return array The array of posts including the custom fields. 
	 */
	public function loadCustomFields(&$posts) {
		
		foreach ($posts as &$post) {
			
			if (false === is_array($post))
				$post = get_object_vars($post);
			
			$post['custom_fields'] = get_post_custom($post['ID']);
		}
		
		return $posts;
		
	}
	
	public function loadCategories(&$posts) {
		foreach ($posts as &$post) {
			if (false === is_array($post))
				$post = get_object_vars($post);

			$post_categories = wp_get_post_categories( $post['ID'] );
			$categories = array();
			
			foreach($post_categories as $category_id){
				$category = get_category( $category_id );
				$categories[] = array(
					'id' => $category_id,
					'name' => $category->name,
					'slug' => $category->slug
				);
			}

			$post['categories'] = $categories;
		}
		
		return $posts;
	}
	
	public function loadAuthors(&$posts) {
		foreach ($posts as &$post) {
			if (false === is_array($post))
				$post = get_object_vars($post);
	
			$author = get_userdata( $post['post_author'] );
			$post['author'] = $author->display_name;
		}
	
		return $posts;
	}
	
	public function loadTags(&$posts) {
		foreach ($posts as &$post) {
			if (false === is_array($post))
				$post = get_object_vars($post);
		
			$post['tags'] = get_the_tags( $post['ID'] );
		}
		
		return $posts;
	}

	public function getPostID(&$post) {
		$id = null;

		if (true === is_numeric($post))
			$id = $post;

		if (true === is_object($post))
			$id = $post->ID;

		if (true === is_array($post))
			$id = $post["ID"];

		if (null === $id)
			throw new Exception("Missing post ID.");

		return $id;
	}

	public function getPostCategories(&$post) {
		$id = $this->getPostID($post);

		$categories = get_the_category($id);
		$category = $categories[0];
		$categories = array($category);
		
		while ('0' !== $category->category_parent) {
			$categories[] = $category = get_category($category->category_parent);
		}
		$categories = array_reverse($categories);
		
		return $categories;
	}
}

?>