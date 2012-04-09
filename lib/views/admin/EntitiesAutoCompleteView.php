<?php

/**
 * This class creates an AutoComplete input text for loading entities via AJAX.
 */
class EntitiesAutoCompleteView {
	
	function __construct() {
		
	}
	
	public function getContent($content='') {
		
		return <<<EOD

		
<script type="text/javascript">
	jQuery(window).ready(function() {
		(function($,_wl,_wls) {
			$( '#entity-name' ).autocomplete({
				source: function( request, response ) {
					$.ajax({
						url: WORDLIFT_20_URL+'api/e.php',
						dataType: "jsonp",
						data: {
							name: request.term
						},
						success: function( data ) {
							response( $.map( data.entities, function( item ) {
								return {
									label: item.text + '('+item.type+')',
									value: item.post_id
								}
							}));
						}
					});
				},
				minLength: 2,
				select: function( event, ui ) {
					if (!ui.item) return;

					_wls.postService.bindEntity(WORDLIFT_20_POST_ID,ui.item.value);
					
					return false;
				},
				open: function() {
					$( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
				},
				close: function() {
					$( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
				}
			});
		})(jQuery,io.insideout.wordlift,io.insideout.wordlift.services);
	});
	</script>

<div class="ui-widget">
	Add an entity: <input id="entity-name" />
</div>

		
EOD;
		
	}
	
}

?>