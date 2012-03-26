<?php

class EntityTileView {

	private $entity;
	
	public function __construct(&$entity) {
		$this->entity = $entity;
	}
	
	public function display() {
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

?>
		<div onclick="location.href='<?php echo $url ?>';" class="isotope-item entity-item <?php echo $type.' '.$relative_rank_class ?>" itemscope itemtype="http://schema.org/<?php echo $type ?>">

		<div class="back">
<?php if (NULL != $image) { ?>
			<div class="image"><img style="width: 100%;" alt="" onerror="jQuery(this).remove();" src="<?php echo $image ?>" /></div>
<?php } else { ?>		
			<div class="description-outer">
			<div class="description">
<?php 	echo $description ?>
			</div>
			</div>
<?php }?>
		</div>

		<div class="front textual">
		<div class="label">
		<a itemprop="name" href="<?php echo $url ?>"><?php echo $label ?></a>
		</div>
		<div class="type"></div>
		</div>
		
		</div>

<?php
	}
}

?>