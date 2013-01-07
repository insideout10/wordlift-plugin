( ($) ->

	$('.entity-container').arrowscrollers
		settings:
			arrow:
				width:36

	$( ".entity-autocomplete" )
		.autocomplete
            minLength: 0
            source: ( request, response ) ->
            	$.ajax
            		url: "wp-admin/admin-ajax.php?action=wordlift.entities&limit=999&name=" + escape( "^#{request.term}" ) + "&order=" + escape( "?name" )
            		success: ( data, status, xhr ) ->
            			response data.content
            		error: ( xhr, status, error ) ->
            			response null
            select: ( event, ui ) ->
            	window.location.replace "wp-admin/admin-ajax.php?action=wordlift.gotoentity&e=" + escape( ui.item.subject )
		.data( "autocomplete" )._renderItem = ( ul, item ) ->
			simpleTypeName = item.a.match( /([^\/]*)$/ )[1]
			$( "<li>" )
				.data( "item.autocomplete", item )
				.append( "<a>#{item.name}</a><div class=\"type #{simpleTypeName}\" />" )
				.appendTo( ul )

	p = "&p=" + ( "#{escape(postId.value)}" for postId in $( "input[name=postId]" ) ).toString()
	$.ajax
		url: "wp-admin/admin-ajax.php?action=wordlift.textannotations#{p}"
		success: ( data, status, xhr ) ->
			console.log data
			$( "##{ann.textAnnotation.replace(':','\\:')}" ).addClass "selected" for ann in data.content


)(jQuery)