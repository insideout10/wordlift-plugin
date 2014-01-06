angular.module('wordlift.tinymce.plugin.services.EditorService', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services.AnnotationService'])
  .service('EditorService', ['AnnotationService', '$rootScope', (AnnotationService, $rootScope) ->

    $rootScope.$on 'DisambiguationWidget.entitySelected', (event, entity) ->
      console.log "Going to map entity #{entity['@id']} on textAnnotation #{entity['dc:relation']}"

      cssClasses = "textannotation #{entity['wordlift:cssClasses']} disambiguated"

      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'class', cssClasses);
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemscope', 'itemscope');
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemtype',  entity['wordlift:supportedTypes'].join(' '));
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemprop', 'name');
      tinyMCE.get("content").dom.setAttrib(entity['dc:relation'], 'itemid', entity['enhancer:entity-reference']);

    $rootScope.$on 'AnnotationService.annotations', (event, annotations) ->
      console.log 'I received some annotations'

      currentHtmlContent = tinyMCE.get('content').getContent({format : 'raw'})

      matches = []

      for textAnnotation in annotations
        # get the selection prefix and suffix for the regexp.
        selPrefix = textAnnotation['enhancer:selection-prefix']['@value'].substr(-2).replace('\\', '\\\\').replace( '\(', '\\(' ).replace( '\)', '\\)').replace('\n', '\\n')
        selPrefix = '^' if '' is selPrefix
        selSuffix = textAnnotation['enhancer:selection-suffix']['@value'].substr(0, 2).replace('\\', '\\\\').replace( '\(', '\\(' ).replace( '\)', '\\)' ).replace('\n', '\\n')
        selSuffix = '$' if '' is selSuffix

        selText   = textAnnotation['enhancer:selected-text']['@value']

        # this is the old regular expression which is not accurate.
        # regexp = new RegExp( "(\\W|^)(#{selText})(\\W|$)(?![^<]*\">?)" )

        # the new regular expression, may not match everything.
        # TODO: enhance the matching.
        r = new RegExp("(#{selPrefix})(#{selText})(#{selSuffix})(?![^<]*\">?)")
        
        replace = "$1<span id=\"#{textAnnotation['@id']}\" class=\"textannotation\"
                          typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</span>$3"

        currentHtmlContent = currentHtmlContent.replace( r, replace )

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