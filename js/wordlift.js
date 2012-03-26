jQuery(window).ready(function($) {

	$('.entity-item').hover(function(eventObject){
		
		$('.entity-item').removeClass('selected');
		
		if ('mouseenter' === eventObject.type)
			$(this).addClass('selected');

	});
	
});
