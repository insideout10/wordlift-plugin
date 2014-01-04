angular.module('wordlift.tinymce.plugin.config', [])
angular.module('wordlift.tinymce.plugin.services', ['wordlift.tinymce.plugin.config'])
  .service('EditorService', ['AnnotationService', (AnnotationService) ->

    ping: (message)    -> console.log message
    analyze: (content) -> AnnotationService.analyze content

  ])
  .service('AnnotationService', [ ->

    analyze: (content) ->
      console.log "ajaxurl: #{ajaxurl}"

      $.ajax
        type: 'POST'
        url:  ajaxurl
        data: content
        success: (data) ->
          console.log data
          r = $.parseJSON(data)
          textAnnotations = r['@graph'].filter (item) ->
            'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?
          console.log textAnnotations
  ])
angular.module('wordlift.tinymce.plugin.controllers', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services'])
  .controller('HelloController', ['EditorService', '$scope', (EditorService, $scope) ->

    $scope.hello = 'Ciao Marcello!'

  ])
$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('<div id="wordlift-tinymce-plugin" ng-controller="HelloController">{{hello}}</div>')
    .appendTo('body')
    .width(1000)
    .height(1000)

  injector = angular.bootstrap(container, ['wordlift.tinymce.plugin']);

  tinymce.PluginManager.add 'wordlift', (editor, url) ->
    # Add a button that opens a window
    editor.addButton 'wordlift',
    text   : 'WordLift'
    icon   : false
    onclick: ->
      alert(tinyMCE.activeEditor.getContent({format : 'text'}))
      injector.invoke(['EditorService', (EditorService) ->
        EditorService.analyze tinyMCE.activeEditor.getContent({format : 'text'})
      ])
#      content = tinyMCE.activeEditor.getContent({format : 'text'})
#      data =
#        action: 'wordlift_analyze'
#        body: content

)



#$ = jQuery
#tinymce.PluginManager.add 'wordlift', (editor, url) ->
#
#	# Add a button that opens a window
#	editor.addButton 'wordlift',
#		text   : 'WordLift'
#		icon   : false
#		onclick: ->
#			content = tinyMCE.activeEditor.getContent({format : 'text'})
#			data =
#				action: 'wordlift_analyze'
#				body: content

#			$.ajax
#				type: "POST"
#				url: ajaxurl
#				data: data
#				success: (data) ->
#					r = $.parseJSON(data)
#					textAnnotations = r['@graph'].filter (item) ->
#						'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?
#
#					currentHtmlContent = tinyMCE.get('content').getContent({format : 'raw'})
#
#					for textAnnotation in textAnnotations
#						console.log(textAnnotation)
#						selectionHead = textAnnotation['enhancer:selection-prefix']['@value']
#							.replace( '\(', '\\(' )
#							.replace( '\)', '\\)' )
#						selectionTail = textAnnotation['enhancer:selection-suffix']['@value']
#							.replace( '\(', '\\(' )
#							.replace( '\)', '\\)' )
#						regexp = new RegExp( "(\\W)(#{textAnnotation['enhancer:selected-text']['@value']})(\\W)(?![^>]*\")" )
#						replace = "$1<strong id=\"#{textAnnotation['@id']}\" class=\"textannotation\"
#							typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</strong>$3"
#						currentHtmlContent = currentHtmlContent.replace( regexp, replace )
#
#						isDirty = tinyMCE.get( "content").isDirty()
#						tinyMCE.get( "content").setContent( currentHtmlContent )
#						tinyMCE.get( "content").isNotDirty = 1 if not isDirty




