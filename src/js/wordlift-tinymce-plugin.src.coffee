angular.module('wordlift.tinymce.plugin.config', [])
angular.module('wordlift.tinymce.plugin.services', ['wordlift.tinymce.plugin.config'])
  .service('EditorService', ['AnnotationService', '$rootScope', (AnnotationService, $rootScope) ->

    $rootScope.$on 'AnnotationService.annotations', (event, annotations) ->
      console.log 'I received some annotations'

      currentHtmlContent = tinyMCE.get('content').getContent({format : 'raw'})

      for textAnnotation in annotations
        console.log(textAnnotation)
        selectionHead = textAnnotation['enhancer:selection-prefix']['@value']
          .replace( '\(', '\\(' )
          .replace( '\)', '\\)' )
        selectionTail = textAnnotation['enhancer:selection-suffix']['@value']
          .replace( '\(', '\\(' )
          .replace( '\)', '\\)' )
        regexp = new RegExp( "(\\W|^)(#{textAnnotation['enhancer:selected-text']['@value']})(\\W|$)(?![^<]*\">?)" )
        console.log regexp
        replace = "$1<strong id=\"#{textAnnotation['@id']}\" class=\"textannotation\"
          typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</strong>$3"
        currentHtmlContent = currentHtmlContent.replace( regexp, replace )

        isDirty = tinyMCE.get( "content").isDirty()
        tinyMCE.get( "content").setContent( currentHtmlContent )
        tinyMCE.get( "content").isNotDirty = 1 if not isDirty

      tinyMCE.get( "content").onClick.add (editor, e) ->
        $rootScope.$apply(
          console.log("Click within the editor on element with id #{e.target.id}")
          $rootScope.$broadcast 'EditorService.annotationClick', e.target.id
        )

    ping: (message)    -> console.log message
    analyze: (content) -> AnnotationService.analyze content

  ])
  .service('AnnotationService', ['$rootScope', '$http', ($rootScope, $http) ->
    
    $rootScope.$on 'EditorService.annotationClick', (event, id) ->
      console.log "Ops!! Element with id #{id} was clicked!"
      findEntitiesForAnnotation(id)
    currentAnalysis = {}
    notifyAnnotations = () ->
      textAnnotations = currentAnalysis['@graph'].filter (item) ->
          'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?
      $rootScope.$broadcast 'AnnotationService.annotations', textAnnotations    
    
    findEntitiesForAnnotation = (annotationId) ->
      console.log "Going to find entities for annotation with ID #{annotationId}"
      entityAnnotations = currentAnalysis['@graph'].filter (item) ->
          'enhancer:EntityAnnotation' in item['@type'] and item['dc:relation'] == annotationId
      $rootScope.$broadcast 'AnnotationService.entityAnnotations', entityAnnotations    
    
    analyze: (content) ->
      $http
        method: 'POST'
        url: ajaxurl
        params:
          action: 'wordlift_analyze'
        data: content
      .success (data, status, headers, config) -> 
        currentAnalysis = data
        notifyAnnotations()
      true
  ])
angular.module('wordlift.tinymce.plugin.controllers', [
	'wordlift.tinymce.plugin.config', 
	'wordlift.tinymce.plugin.services'
	])
  .controller('HelloController', ['AnnotationService', '$scope', (AnnotationService, $scope) ->

    $scope.hello       = 'Ciao Marcello!'
    $scope.annotations = []

    $scope.$on 'AnnotationService.entityAnnotations', (event, annotations) ->
      console.log 'I received entity annotations too'
      console.log annotations
      $scope.annotations = annotations
    
  ])
$ = jQuery

angular.module('wordlift.tinymce.plugin', ['wordlift.tinymce.plugin.controllers'])

$(
  container = $('''
    <div id="wordlift-tinymce-plugin" ng-controller="HelloController">{{hello}}
      <h2>Debug</h2>
      <ul>
        <li ng-repeat="annotation in annotations">
          <div>annotation</div>
          <div ng-bind="annotation['enhancer:entity-reference']"></div>
        </li>
      </ul>
    </div>
    ''')
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
      injector.invoke(['EditorService', (EditorService) ->
        EditorService.analyze tinyMCE.activeEditor.getContent({format : 'text'})
      ])


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




