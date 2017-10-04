$(document).ready(function() {
				
	$('.glossary-alphabet-nav a').on('click', function(e) {
		e.preventDefault();
					
		var fullUrl = $(this).attr('href'),
			parts = fullUrl.split('#'),
			target = parts[1],
			navHeight = $('.glossary-alphabet-nav').height(),
			targetOffset = $('#'+target).offset(),
			targetTop = targetOffset.top - navHeight;
						
			$('html, body').animate({scrollTop: targetTop}, 500);
						
			$('.glossary-alphabet-nav a').removeClass('active');
			$(this).addClass('active');
					
	});
});
