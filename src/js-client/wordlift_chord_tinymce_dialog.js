jQuery(document).ready(function($){
	$('body').append(
		'<div id="wordlift_chord_dialog">' +
			'<form>' +
				'<p>' +		
					'<input value="7" id="wordlift_chord_depth_field" readonly size="3">' +
					'Depth: how many entities the Graph will display.' +
					'<div id="wordlift_chord_depth_slider"></div>' +
				'</p><br>' +
				'<p>' +
					'Base to generate the color palette of the Graph.<br>' +
					'<input type="text" value="#22f" id="wordlift_chord_color_field" size="4">' +
				'</p><br>' +
				'<p>' +
					'<input value="500" id="wordlift_chord_width_field" size="4">' +
					'Width of the Graph in pixels.' +
				'</p><br>' +
				'<p>' +
					'<input value="500" id="wordlift_chord_height_field" size="4">' +
					'Height of the Graph in pixels.' +
				'</p><br>' +
				'<p>' +
					'<input id="wordlift_chord_dialog_ok" type="button" value="Ok" width="100">' +
				'</p>' +
			'</form>' +
		'</div>'
	);
	
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
	
	// Generatr shortcode.
	$('#wordlift_chord_dialog_ok').on('click', function(){
		
		//we should get default parameters from the php
		var width = $('#wordlift_chord_width_field').val();
		var height = $('#wordlift_chord_height_field').val();
		var main_color = $('#wordlift_chord_color_field').val();
		var depth = $('#wordlift_chord_depth_field').val();

		var shortcode_text = '[wl-chord width=' + width + 'px' +
										  ' height=' + height + 'px' +
										  ' main_color=' + main_color +
										  ' depth=' + depth + ']';
										  
		// Send shortcode to the editor								  
		top.tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode_text);
		$('#wordlift_chord_dialog').dialog('close');
	});
});
