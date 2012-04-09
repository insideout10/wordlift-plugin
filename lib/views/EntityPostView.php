<?php

/**
 * Displays the contents for an Entity.
 */
class EntityPostView implements IView {

	// The logging instance.
	private $logger;
	
	private $entity;
	
	/**
	 * Creates an instance of the EntityPostView.
	 */
	function __construct(&$entity) {
		$this->logger = Logger::getLogger(__CLASS__);
		$this->entity = $entity;
	}
	
	public function getContent($content=null) {
		$title = htmlentities( $this->entity->text );
		$type = htmlentities( $this->entity->type );
		$about = htmlentities( $this->entity->about );
		$image = htmlentities( $this->entity->properties['image'][0] );
		$description = htmlentities( $this->entity->properties['description'][0] );
		
		$latitude = $this->entity->properties['geo-latitude'][0];
		$longitude = $this->entity->properties['geo-longitude'][0];
		
		$posts = $this->entity->accepted_posts;
		$posts_list_view = new PostsListView($posts);
		
		$blog_postings_service = new BlogPostingService();
		$blog_postings = $blog_postings_service->fromPosts($posts);
		
		$blog_posting_list_view = new BlogPostingListView($blog_postings);
		
		$content = <<< EOD
		
		<div class="entity-posts">{$blog_posting_list_view->getContent()}</div>
		
		<div class="entity">
			<div class="entity-inner">
			
			<div class="title">$title</div>
			<div class="image"><img onerror="jQuery(this).remove();" src="$image" /></div>
			<div class="type">$type</div>
			<div class="about">$about</div>
			<div class="description">$description</div>
			</div>
EOD;

		if ('' != $latitude && '' != $longitude) {
			$map_view = new MapView($latitude,$longitude,'map',10);
			$content .= '<div id="map"></div>';
			$content .= $map_view->getContent();
		}

		$content .= '</div>';
		
		return $content;
			
	}
	
	public function display() {
		echo $this->getContent();
	}
	
}

?>