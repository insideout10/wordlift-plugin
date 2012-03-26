<?php

class AllEntitiesView {
	
	private $entities;
	
	public function __construct(&$entities) {
		$this->entities = $entities;
	}
	
	public function display() {
		
		echo '<div id="entities-container">';
		
		foreach ($this->entities as $entity) {
			$entity_tile_view = new EntityTileView($entity);
			$entity_tile_view->display();
		}
		
		echo '</div>';
?>

	<script type="text/javascript">
		jQuery(window).ready(function($){
			$('#entities-container').isotope({
				itemSelector : '.isotope-item',
				layoutMode : 'masonry'
			});
			
		});
	</script>

<?php
 
	}
}

?>