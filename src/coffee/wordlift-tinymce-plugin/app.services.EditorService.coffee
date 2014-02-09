angular.module('wordlift.tinymce.plugin.services.EditorService', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services.AnnotationService'])
  .service('EditorService', ['AnnotationService', '$rootScope', '$log', 'Configuration', (AnnotationService, $rootScope, $log, Configuration) ->

    # this event is captured when an entity is selected in the disambiguation popover.
    $rootScope.$on 'DisambiguationWidget.entitySelected', (event, obj) ->
      cssClasses = "textannotation highlight #{obj.entity.type} disambiguated"

      # create a reference to the TinyMCE editor dom.
      dom  = tinyMCE.get("content").dom
      # the element id containing the attributes for the text annotation.
      id   = obj.relation.id
      elem = dom.get(id)
 
      dom.setAttrib(id, 'class', cssClasses);
      dom.setAttrib(id, 'itemscope', 'itemscope');
      dom.setAttrib(id, 'itemtype',  obj.entity.type);
      dom.setAttrib(id, 'itemid', obj.entity.id);

    # receive annotations from the analysis.
    $rootScope.$on 'analysisReceived', (event, analysis) ->

      # clean up the selection prefix/suffix text.
      cleanUp = (text) ->
        text
          .replace('\\', '\\\\').replace( '\(', '\\(' ).replace( '\)', '\\)').replace('\n', '\\n?')
          .replace('-', '\\-').replace('\x20', '\\s').replace('\xa0', '&nbsp;')

      currentHtmlContent = tinyMCE.get('content').getContent({format : 'raw'})

      for id, textAnnotation of analysis.textAnnotations
        # get the selection prefix and suffix for the regexp.
        selPrefix = cleanUp(textAnnotation.selectionPrefix.substr(-1))
        selPrefix = '^|\\W' if '' is selPrefix
        selSuffix = cleanUp(textAnnotation.selectionSuffix.substr(0, 1))
        selSuffix = '$|\\W' if '' is selSuffix

        selText   = textAnnotation.selectedText

        # the new regular expression, may not match everything.
        # TODO: enhance the matching.
        r = new RegExp("(#{selPrefix}(?:<[^>]+>){0,})(#{selText})((?:<[^>]+>){0,}#{selSuffix})(?![^<]*\"[^<]*>)")

#        if not currentHtmlContent.match(r)?
#          $log.debug r
#          $log.debug currentHtmlContent
#
        replace = "$1<span id=\"#{id}\" class=\"textannotation\" typeof=\"http://fise.iks-project.eu/ontology/TextAnnotation\">$2</span>$3"

        currentHtmlContent = currentHtmlContent.replace( r, replace )

      isDirty = tinyMCE.get('content').isDirty()
      tinyMCE.get('content').setContent( currentHtmlContent )
      tinyMCE.get('content').isNotDirty = 1 if not isDirty

      # this event is raised when a textannotation is selected in the TinyMCE editor.
      tinyMCE.get('content').onClick.add (editor, e) ->
        # execute the following commands in the angular js context.
        $rootScope.$apply(
          $log.debug "Going to notify click on annotation with id #{e.target.id}"
          # send a message about the currently clicked annotation.
          $rootScope.$broadcast 'textAnnotationClicked', e.target.id, e
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
