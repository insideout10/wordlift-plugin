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
        regexp = new RegExp( "(\\W)(#{textAnnotation['enhancer:selected-text']['@value']})(\\W)(?![^>]*\")" )
        replace = "$1<strong id=\"#{textAnnotation['@id']}\" class=\"textannotation\"
          typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</strong>$3"
        currentHtmlContent = currentHtmlContent.replace( regexp, replace )

        isDirty = tinyMCE.get( "content").isDirty()
        tinyMCE.get( "content").setContent( currentHtmlContent )
        tinyMCE.get( "content").isNotDirty = 1 if not isDirty

    ping: (message)    -> console.log message
    analyze: (content) -> AnnotationService.analyze content

  ])
  .service('AnnotationService', ['$rootScope', ($rootScope) ->

    analyze: (content) ->
      console.log "ajaxurl: #{ajaxurl}"

      $.ajax
        type: 'POST'
        url:  ajaxurl
        data:
          action: 'wordlift_analyze'
          body: content
        success: (data) ->
          console.log data
          r = $.parseJSON(data)
          textAnnotations = r['@graph'].filter (item) ->
            'enhancer:TextAnnotation' in item['@type'] and item['enhancer:selection-prefix']?
          console.log textAnnotations

          $rootScope.$apply(
            $rootScope.$broadcast 'AnnotationService.annotations', textAnnotations
          )
  ])