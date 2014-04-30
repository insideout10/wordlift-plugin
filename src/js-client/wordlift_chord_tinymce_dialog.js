jQuery(document).ready(function($){
	
	//set up color pickker
	$('#wordlift_chord_color_field').wpColorPicker({
		hide: true
	});
	
	//set up depth slider
	$('#wordlift_chord_depth_slider').slider({
		range: 'max',
		min: 3,
		max: 30,
		value: 7,
		slide: function( event, ui ) {
			$( "#wordlift_chord_depth_field" ).val( ui.value );
		}
	});
	
	$('#wordlift_chord_dialog').hide();
	
	$('#wordlift_chord_dialog_ok').on('click', function(){
		
		//we should get default parameters from the php
		var width = $('#wordlift_chord_width_field').val();
		var height = $('#wordlift_chord_height_field').val();
		var main_color = $('#wordlift_chord_color_field').val();
		var depth = $('#wordlift_chord_depth_field').val();
		shortcode_text = '[wl-chord-widget width=' + width + 'px' +
										  ' height=' + height + 'px' +
										  ' main_color=' + main_color +
										  ' depth=' + depth + ']';
		top.tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode_text);
		$('#wordlift_chord_dialog').dialog('close');
	});
});
