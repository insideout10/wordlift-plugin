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