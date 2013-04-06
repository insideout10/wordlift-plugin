$ = jQuery

$ ->
	$("#wordlift-bar-switch").click ->
		$("#wordlift-bar").toggleClass("closed")

	$(window).scroll (event) ->
		if 0 >= $("body").height() - $(window).height() - $("body").scrollTop()
			if false is $("#wordlift-bar").hasClass("closed")
				$("#wordlift-bar").addClass("closed").addClass("automatic")
		else
			if $("#wordlift-bar").hasClass("closed") and $("#wordlift-bar").hasClass("automatic")
				$("#wordlift-bar").removeClass("closed").removeClass("automatic")	

