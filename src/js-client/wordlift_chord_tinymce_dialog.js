jQuery(document).ready(function($){
	$('.my-color-field').wpColorPicker();
	console.log("SIAMO PRONTI");
});

function submitChordParams(){
	//console.log(wl_chord_params);
	//we should get default parameters from the php
	var width = '500';
	var height = '400';
	var main_color = '#f2d';
	var depth = '5';
	shortcode_text = '[wl-chord-widget width=' + width + 'px' +
									  ' height=' + height + 'px' +
									  ' main_color=' + main_color +
									  ' depth=' + depth + ']';
	top.tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode_text);
	
}
