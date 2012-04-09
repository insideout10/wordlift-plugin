<?php

/**
 * This class generated the Html code for an Entity tile.
 */
class EntityTileView {

	private $entity;
	
	// The logger instance.
	private $logger;
	
	function __construct(&$entity) {
		$this->logger = $GLOBALS['logger'];
		$this->entity = $entity;
	}
	
	public function getContent($content = '') {
		
// 		$this->logger->debug('Generating content for an entity.');
		
		$url = get_permalink( $this->entity->post_id );
		$label= htmlentities( $this->entity->text, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$type = htmlentities( $this->entity->type, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$about = htmlentities( $this->entity->about, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$image = htmlentities( $this->entity->properties['image'][0], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$description = htmlentities( $this->entity->properties['description'][0], ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$description = ('' == $description ? 'description not available' : $description);
		
		$relative_rank_class = 'rank-'.$this->entity->relative_rank;
		
		$latitude = $this->entity->properties['geo-latitude'][0];
		$longitude = $this->entity->properties['geo-longitude'][0];
		
		$content = '<div onclick="location.href=\''.$url.'\';" class="isotope-item entity-item '.$type.' '.$relative_rank_class.'" itemscope itemtype="http://schema.org/'.$type.'">';
		$content .= '<div class="back">';
		if (NULL != $image) {
			$content .= '<div class="image"><img style="width: 100%;" alt="" onerror="jQuery(this).remove();" src="'.$image.'" /></div>';
		} else {		
			$content .= '<div class="description-outer">';
			$content .= '<div class="description">';
			$content .= $description;
			$content .= '</div>';
			$content .= '</div>';
		}
		$content .= '</div>';
		$content .= '<div class="front textual">';
		$content .= '<div class="label">';
		$content .= '<a itemprop="name" href=".$url.">'.$label.'</a>';
		$content .= '</div>';
		$content .= '<div class="type"></div>';
		$content .= '</div>';
		$content .= '</div>';
				
		return $content;
	}
	
	public function display() {
		echo $this->getContent();
	}
}

?>