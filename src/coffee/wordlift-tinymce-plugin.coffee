$ = jQuery
tinymce.PluginManager.add 'wordlift', (editor, url) ->

	# Add a button that opens a window
	editor.addButton 'wordlift',
		text   : 'WordLift'
		icon   : false
		onclick: -> 
			content = tinyMCE.activeEditor.getContent({format : 'text'})
			data = 
				action: 'wordlift_analyze'
				body: content      

			$.ajax
				type: "POST"
				url: ajaxurl    
				data: data
				success: (data) ->
					r = $.parseJSON(data)
					textAnnotations = r['@graph'].filter (item) -> 
						'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?

					currentHtmlContent = tinyMCE.get('content').getContent({format : 'raw'})
					
					for textAnnotation in textAnnotations
						console.log(textAnnotation)
						selectionHead = textAnnotation['enhancer:selection-prefix']['@value']
							.replace( '\(', '\\(' )
							.replace( '\)', '\\)' )
						selectionTail = textAnnotation['enhancer:selection-suffix']['@value']
							.replace( '\(', '\\(' )
							.replace( '\)', '\\)' )
						regexp = new RegExp( "(\\W)(#{textAnnotation['enhancer:selected-text']['@value']})(\\W)(?![^>]*\")" )
						replace = "$1<strong id=\"#{textAnnotation['@id']}\" class=\"textannotation\"
							typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</strong>$3"
						currentHtmlContent = currentHtmlContent.replace( regexp, replace )

						isDirty = tinyMCE.get( "content").isDirty()
						tinyMCE.get( "content").setContent( currentHtmlContent )
						tinyMCE.get( "content").isNotDirty = 1 if not isDirty	
				   

   
	
	  