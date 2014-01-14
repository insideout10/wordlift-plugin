angular.module('wordlift.tinymce.plugin.services.EditorService', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services.AnnotationService'])
  .service('EditorService', ['AnnotationService', '$rootScope', '$log', 'Configuration', (AnnotationService, $rootScope, $log, Configuration) ->

    # this event is captured when an entity is selected in the disambiguation popover.
    $rootScope.$on 'DisambiguationWidget.entitySelected', (event, entity) ->
      cssClasses = "textannotation #{entity['wordlift:cssClasses']} disambiguated"

      # create a reference to the TinyMCE editor dom.
      dom  = tinyMCE.get("content").dom
      # the element id containing the attributes for the text annotation.
      id   = entity[Configuration.entityLabels.relation]
      elem = dom.get(id)
 
      dom.setAttrib(id, 'class', cssClasses);
      dom.setAttrib(id, 'itemscope', 'itemscope');
      dom.setAttrib(id, 'itemtype',  entity['wordlift:supportedTypes'].join(' '));
      dom.setAttrib(id, 'itemid', entity[Configuration.entityLabels.entityReference]);

      # set the itemprop nested inside the itemscope/itemtype.
      elem.innerHTML = '<span itemprop="name">' + elem.innerHTML + '</span>'

    # receive annotations from the analysis.
    $rootScope.$on 'AnnotationService.annotations', (event, annotations) ->
      $log.debug "receive #{annotations.length} annotation(s)"

      currentHtmlContent = tinyMCE.get('content').getContent({format : 'raw'})

      for textAnnotation in annotations
        # get the selection prefix and suffix for the regexp.
        selPrefix = textAnnotation[Configuration.entityLabels.selectionPrefix]['@value'].substr(-1).replace('\\', '\\\\').replace( '\(', '\\(' ).replace( '\)', '\\)').replace('\n', '\\n?').replace('-', '\\-').replace('\x20', '\\s').replace('\xa0', '&nbsp;')
        selPrefix = '^|\\W' if '' is selPrefix
        selSuffix = textAnnotation[Configuration.entityLabels.selectionSuffix]['@value'].substr(0, 1).replace('\\', '\\\\').replace( '\(', '\\(' ).replace( '\)', '\\)' ).replace('\n', '\\n?').replace('-', '\\-').replace('\x20', '\\s').replace('\xa0', '&nbsp;')
        selSuffix = '$|\\W' if '' is selSuffix

        selText   = textAnnotation[Configuration.entityLabels.selectedText]['@value']

        # the new regular expression, may not match everything.
        # TODO: enhance the matching.
        r = new RegExp("(#{selPrefix}(?:<[^>]+>){0,})(#{selText})((?:<[^>]+>){0,}#{selSuffix})(?![^<]*\"[^<]*>)")

        if not currentHtmlContent.match(r)?
          $log.debug r
          $log.debug currentHtmlContent

        replace = "$1<span id=\"#{textAnnotation['@id']}\" class=\"textannotation\" typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</span>$3"

        currentHtmlContent = currentHtmlContent.replace( r, replace )

      isDirty = tinyMCE.get( "content").isDirty()
      tinyMCE.get( "content").setContent( currentHtmlContent )
      tinyMCE.get( "content").isNotDirty = 1 if not isDirty

      # this event is raised when a textannotation is selected in the TinyMCE editor.
      tinyMCE.get('content').onClick.add (editor, e) ->
        # execute the following commands in the angular js context.
        $rootScope.$apply(
          # send a message about the currently clicked annotation.
          $rootScope.$broadcast 'EditorService.annotationClick', e.target.id, e
        )

    ping: (message)    -> $log.debug message
    analyze: (content) -> AnnotationService.analyze content

    # set some predefined variables.
    getEditor : -> tinyMCE.get('content')
    getBody   : -> @getEditor().getBody()
    getDOM    : -> @getEditor().dom

    # get the window position of an element inside the editor.
    # @param element elem The element.
    getWinPos: (elem) ->
      # get a reference to the editor and its body
      ed   = @getEditor()
      el   = elem.target

      top  = $('#content_ifr').offset().top - $('body').scrollTop() +
             el.offsetTop - $(ed.getBody()).scrollTop()

      left = $('#content_ifr').offset().left - $('body').scrollLeft() +
             el.offsetLeft - $(ed.getBody()).scrollLeft()

      #return the coordinates.
      {top: top, left: left}
])
