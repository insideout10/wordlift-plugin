<?php

/**
 * This class builds a select with the list of allowed schema.org classes.
 */
class TypeSelectionView {
	
	// the type on which this TypeSelectionView is initialized.
	private $type;
	
	function __construct($type = NULL) {
		$this->type = $type;
	}
	
	public function getContent($content='') {

		$types = SchemaOrgFramework::getSchemas();
		
		$schema_type = WORDLIFT_20_FIELD_SCHEMA_TYPE;
		
		$content = '<div class="property">';
		$content .= '<label for="'.WORDLIFT_20_FIELD_SCHEMA_TYPE.'">Schema.org type:</label>';
		$content .= '<select name="'.WORDLIFT_20_FIELD_SCHEMA_TYPE.'" data-type="'.htmlentities($this->type).'">';
		
		foreach ($types as $key => $value) {
			$content .= '<option value="'.htmlentities($value).'"';
			$content .= ($this->type == $value ? 'selected' : '');
			$content .= '>'.htmlentities($key);
			$content .= '</option>';
		}
		
		$content .= '</select>';
		$content .= '</div>';
		
		$content .= <<<EOD

		
<div id="dialog-confirm" style="display:none;" title="Change the Entity type?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 100px 0;"></span>
By changing the Entity type, properties that are not present in the new type will be discarded.<br/><br/>Continue?</p>
</div>

<script type="text/javascript">
	jQuery(window).ready(function($) {
		
		$('select[name="$schema_type"]')
			.change(function(event){
				$(this).blur();
				$('#dialog-confirm').dialog({
					resizable: false,
					width:400,
					height:200,
					modal: true,
					buttons: {
						"Proceed": function() {
							$( this ).dialog('close');
							$('form#post').submit();
						},
						Cancel: function() {
						console.log($(event.target).data('type'));
							$(event.target).val($(event.target).data('type'));
							$(this).dialog('close');
						}
					}
				});
			});
	
	});
</script>
		
EOD;
		
		return $content;
	}

}


?>