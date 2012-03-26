<?php

class EntityPostView {
	
	private $logger;
	
	public function __construct() {
		$this->logger = Logger::getLogger(__CLASS__);
		
		global $map_view;
		if (NULL == $map_view) {
			$this->logger->error('MapView is undefined.');
			return;
		}

		$this->map_view = $map_view;
	}
	
	public function displayPostView(&$entity){
		$title = htmlentities( $entity->text );
		$type = htmlentities( $entity->type );
		$about = htmlentities( $entity->about );
		$image = htmlentities( $entity->properties['image'][0] );
		$description = htmlentities( $entity->properties['description'][0] );
		
		$latitude = $entity->properties['geo-latitude'][0];
		$longitude = $entity->properties['geo-longitude'][0];
		
		$posts = $entity->posts;
?>

<div class="entity-posts">
<?php
		$posts_list_view = new PostsListView($posts);
		$posts_list_view->display();
?>
</div>

<div class="entity">
	<div class="entity-inner">
	
	<div class="title"><?php echo $title ?></div>
	<?php if ($image) ?><div class="image"><img onerror="jQuery(this).remove();" src="<?php echo $image ?>" /></div>
	<div class="type"><?php echo $type ?></div>
	<div class="about"><?php echo $about ?></div>
	<?php if ($description) ?><div class="description"><?php echo $description ?></div>
	
	</div>
	
<?php
		if ('' != $latitude && '' != $longitude) {
			echo '<div id="map"></div>';
			
			$this->map_view->display($latitude,$longitude,'map',10);
		}
?>
</div>
	
<?php
		
	}
	
}

$entity_post_view = new EntityPostView();
?>