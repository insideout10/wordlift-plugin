<?php

/**
 * This class displays a treemap of the entities passed in the constructor.
 */
class EntitiesTreemapView implements IShortCode {
	
	// the shortcode bound to this view.
	const SHORT_CODE = 'entities.treemap';

	// the entities shall be set while initializing the class.
	private $entities;

	/**
	 * Creates a new instance by passing the entities to represent.
	 * @param Array $entities
	 */
	function __construct(&$entities) {
		$this->entities = $entities;
	}
	
	/************************************************************************************
	 *  IShortCode implementation.														*
	 ************************************************************************************/
	
	/**
	 * Returns the Shot Code for this view.
	 */
	public static function getShortCode() {
		return self::SHORT_CODE;
	}

	/**
	 * Returns the Shot Code for this view.
	 */
	public static function doShortCode($atts, $content=null, $tag=null) {
		$entity_service = new EntityService();
		$entities = $entity_service->get_all_accepted_entities();
		$entity_ranking_service = new EntityRankingService();
		$entity_ranking_service->rank($entities);
		
		$self = new self($entities);
		return $self->getContent();
	}
	
	/************************************************************************************/
	
	/**
	 * Returns the content to display a treemap of entities.
	 * @param string $content
	 */
	public function getContent($content='') {
	
		$content = <<<EOD
		
		[<a href="javascript:jQuery('#entities-container').isotope({ filter: '' });">All</a>]<br />
		[<a	href="javascript:jQuery('#entities-container').isotope({ filter: '.Person' });">People</a>]
		[<a	href="javascript:jQuery('#entities-container').isotope({ filter: '.Place' });">Places</a>]
		[<a href="javascript:jQuery('#entities-container').isotope({ filter: '.CreativeWork' });">Creative Works</a>]
		[<a	href="javascript:jQuery('#entities-container').isotope({ filter: '.Organization' });">Organizations</a>]
		[<a href="javascript:jQuery('#entities-container').isotope({ filter: '.Other' });">Non classified</a>]<br />

		<div id="entities-container" class="all-entities-view">

EOD;
		
		foreach ($this->entities as $entity) {
			$entity_tile_view = new EntityTileView($entity);
			$content .= $entity_tile_view->getContent();
		}

		$content .= <<< EOD

		</div>

		<script type="text/javascript">
				jQuery(window).ready(function($){
					$('#entities-container').isotope({
						itemSelector : '.isotope-item',
						layoutMode : 'masonry',
						masonry: {
							columnWidth:1
						}
					});
		
					$('#entities-container').isotope('shuffle');
				});
			</script>
EOD;
		
		return $content;
		
	}
		
	public function display() {
		
		echo $this->getContent();
 
	}
}

?>