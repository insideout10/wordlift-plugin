angular.module('wordlift.tinymce.plugin.services.EditorService', ['wordlift.tinymce.plugin.config', 'wordlift.tinymce.plugin.services.AnnotationService'])
  .service('EditorService', ['AnnotationService', '$rootScope', (AnnotationService, $rootScope) ->

    # this event is captured when an entity is selected in the disambiguation popover.
    $rootScope.$on 'DisambiguationWidget.entitySelected', (event, entity) ->
      cssClasses = "textannotation #{entity['wordlift:cssClasses']} disambiguated"

      # create a reference to the TinyMCE editor dom.
      dom  = tinyMCE.get("content").dom
      # the element id containing the attributes for the text annotation.
      id   = entity['dc:relation']
      elem = dom.get(id)

      dom.setAttrib(id, 'class', cssClasses);
      dom.setAttrib(id, 'itemscope', 'itemscope');
      dom.setAttrib(id, 'itemtype',  entity['wordlift:supportedTypes'].join(' '));
      dom.setAttrib(id, 'itemid', entity['enhancer:entity-reference']);

      # set the itemprop nested inside the itemscope/itemtype.
      elem.innerHTML = '<span itemprop="name">' + elem.innerHTML + '</span>'

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

      # this event is raised when a textannotation is selected in the TinyMCE editor.
      tinyMCE.get('content').onClick.add (editor, e) ->
        # execute the following commands in the angular js context.
        $rootScope.$apply(
          # send a message about the currently clicked annotation.
          $rootScope.$broadcast 'EditorService.annotationClick', e.target.id, e
        )

    ping: (message)    -> console.log message
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
      body = $(@getBody())
      el   = elem.target

      top  = $('#content_ifr').offset().top - $('body').scrollTop() +
             el.offsetTop - $(ed.getBody()).scrollTop()

      left = $('#content_ifr').offset().left - $('body').scrollLeft() +
             el.offsetLeft - $(ed.getBody()).scrollLeft()

      # get the position of the element inside the body
      elx = elem.srcElement.offsetLeft
      ely = elem.srcElement.offsetTop
      # get the position of the editor container inside the root document.
      coffset = $(ed.getContainer()).offset()
      # calculate window coordinates.
      x = elx + coffset.left - body.scrollTop() - $(window).scrollTop()
      y = ely + coffset.top - body.scrollLeft() - $(window).scrollLeft()
      #return the coordinates.
      {top: top, left: left}
])
