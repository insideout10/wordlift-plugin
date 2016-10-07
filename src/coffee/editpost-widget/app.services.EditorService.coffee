# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.editpost.widget.services.EditorService', [
  'wordlift.editpost.widget.services.AnalysisService'
  ])
# Manage redlink analysis responses
.service('EditorService', [ 'configuration', 'AnalysisService', '$log', '$http', '$rootScope', (configuration, AnalysisService, $log, $http, $rootScope)-> 
  
  INVISIBLE_CHAR = '\uFEFF'
  BLIND_ANNOTATION_CSS_CLASS = 'no-entity-page-link'

  # Find existing entities selected in the html content (by looking for *itemid* attributes).
  findEntities = (html) ->
    # Prepare a traslator instance that will traslate Html and Text positions.
    traslator = Traslator.create html
    # Set the pattern to look for *itemid* attributes.
    pattern = /<(\w+)[^>]*\sitemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim

    # Get the matches and return them.
    (while match = pattern.exec html
      
      
      annotation = 
        start: traslator.html2text match.index
        end: traslator.html2text (match.index + match[0].length)
        uri: match[2]
        label: match[3]
        # Detect if this annotation has link related entity page or not
        isBlind: (match[0].indexOf(BLIND_ANNOTATION_CSS_CLASS) isnt -1)
      
      $log.debug annotation

      annotation
    )

  findPositions = ( entities ) ->
    positions = []
    for entityAnnotation in entities 
      positions = positions.concat [ entityAnnotation.start..entityAnnotation.end ]
    positions   

  editor = ->
    tinyMCE.get('content')
    
  disambiguate = ( annotationId, entity )->
    ed = editor()
    ed.dom.addClass annotationId, "disambiguated"
    
    for type in configuration.types
      ed.dom.removeClass annotationId, type.css
    
    ed.dom.removeClass annotationId, "unlinked"
    ed.dom.removeClass annotationId, BLIND_ANNOTATION_CSS_CLASS
    
    discardedItemId = ed.dom.getAttrib annotationId, "itemid"
    ed.dom.setAttrib annotationId, "itemid", entity.id
    discardedItemId

  dedisambiguate = ( annotationId, entity )->
    ed = editor()
    ed.dom.removeClass annotationId, "disambiguated"
    ed.dom.removeClass annotationId, BLIND_ANNOTATION_CSS_CLASS
    ed.dom.removeClass annotationId, "wl-#{entity.mainType}"
    
    discardedItemId = ed.dom.getAttrib annotationId, "itemid"
    ed.dom.setAttrib annotationId, "itemid", ""
    discardedItemId

  # TODO refactoring with regex
  currentOccurencesForEntity = (entityId) ->
    ed = editor()
    occurrences = []    
    return occurrences if entityId is ""
    annotations = ed.dom.select "span.textannotation"
    for annotation in annotations
      itemId = ed.dom.getAttrib annotation.id, "itemid"
      occurrences.push annotation.id  if itemId is entityId
    occurrences

  $rootScope.$on "analysisPerformed", (event, analysis) ->
    service.embedAnalysis analysis if analysis? and analysis.annotations?
  
  # Disambiguate a single annotation or every entity related ones
  # Discarded entities are considered too
  $rootScope.$on "entitySelected", (event, entity, annotationId) ->
    discarded = []
    if annotationId?
      discarded.push disambiguate annotationId, entity
    else    
      for id, annotation of entity.annotations
        discarded.push disambiguate annotation.id, entity
    
    for entityId in discarded
      if entityId
        occurrences = currentOccurencesForEntity entityId
        $rootScope.$broadcast "updateOccurencesForEntity", entityId, occurrences

    occurrences = currentOccurencesForEntity entity.id
    $rootScope.$broadcast "updateOccurencesForEntity", entity.id, occurrences
      
  $rootScope.$on "entityDeselected", (event, entity, annotationId) ->
    if annotationId?
      dedisambiguate annotationId, entity
    else
      for id, annotation of entity.annotations
        dedisambiguate annotation.id, entity
        
    occurrences = currentOccurencesForEntity entity.id    
    $rootScope.$broadcast "updateOccurencesForEntity", entity.id, occurrences
  
  $rootScope.$on "entityBlindnessToggled", (event, entity) ->
    ed = editor()
    for annotationId in entity.occurrences
      ed.dom.removeClass annotationId, BLIND_ANNOTATION_CSS_CLASS
    for annotationId in entity.blindOccurrences
      ed.dom.addClass annotationId, BLIND_ANNOTATION_CSS_CLASS

  service =
    # Detect if there is a current selection
    hasSelection: ()->
      # A reference to the editor.
      ed = editor()
      if ed?
        if ed.selection.isCollapsed()
          return false
        pattern = /<([\/]*[a-z]+)[^<]*>/
        if pattern.test ed.selection.getContent()
          $log.warn "The selection overlaps html code"
          return false
        return true 

      false

    # Check if the given editor is the current editor
    isEditor: (editor)->
      ed = editor()
      ed.id is editor.id

    # Update contenteditable status for the editor
    updateContentEditableStatus: (status)->
      # A reference to the editor.
      ed = editor() 
      ed.getBody().setAttribute 'contenteditable', status

    # Create a textAnnotation starting from the current selection
    createTextAnnotationFromCurrentSelection: ()->
      # A reference to the editor.
      ed = editor()
      # If the current selection is collapsed / blank, then nothing to do
      if ed.selection.isCollapsed()
        $log.warn "Invalid selection! The text annotation cannot be created"
        return

      # Retrieve the selected text
      # Notice that toString() method of browser native selection obj is used
      text = "#{ed.selection.getSel()}"
      # Create the text annotation
      textAnnotation = AnalysisService.createAnnotation { 
        text: text
      }

      # Prepare span wrapper for the new text annotation
      textAnnotationSpan = "<span id=\"#{textAnnotation.id}\" class=\"textannotation unlinked selected\">#{ed.selection.getContent()}</span>#{INVISIBLE_CHAR}"
      # Update the content within the editor
      ed.selection.setContent textAnnotationSpan 
      
      # Retrieve the current heml content
      content = ed.getContent format: 'raw'
      # Create a Traslator instance
      traslator =  Traslator.create content
      # Retrieve the index position of the new span
      htmlPosition = content.indexOf(textAnnotationSpan);
      # Detect the coresponding text position
      textPosition = traslator.html2text htmlPosition 
          
      # Set start & end text annotation properties
      textAnnotation.start = textPosition 
      textAnnotation.end = textAnnotation.start + text.length
      
      # Send a message about the new textAnnotation.
      $rootScope.$broadcast 'textAnnotationAdded', textAnnotation

    # Select annotation with a id annotationId if available
    selectAnnotation: (annotationId)->
      # A reference to the editor.
      ed = editor()
      # Unselect all annotations 
      for annotation in ed.dom.select "span.textannotation"
        ed.dom.removeClass annotation.id, "selected"
      # Notify it
      $rootScope.$broadcast 'textAnnotationClicked', undefined
      # If current is a text annotation, then select it and notify
      if ed.dom.hasClass annotationId, "textannotation"
        ed.dom.addClass annotationId, "selected"
        $rootScope.$broadcast 'textAnnotationClicked', annotationId

    # Embed the provided analysis in the editor.
    embedAnalysis: (analysis) =>
      # A reference to the editor.
      ed = editor()
      # Get the TinyMCE editor html content.
      html = ed.getContent format: 'raw'
      $log.debug html
      # Find existing entities.
      entities = findEntities html
      # Remove overlapping annotations preserving selected entities
      AnalysisService.cleanAnnotations analysis, findPositions(entities)
      # Preselect entities found in html.
      AnalysisService.preselect analysis, entities

      # Remove existing text annotations (the while-match is necessary to remove nested spans).
      while html.match(/<(\w+)[^>]*\sclass="textannotation[^"]*"[^>]*>([^<]+)<\/\1>/gim, '$2')
        html = html.replace(/<(\w+)[^>]*\sclass="textannotation[^"]*"[^>]*>([^<]*)<\/\1>/gim, '$2')

      # Prepare a traslator instance that will traslate Html and Text positions.
      traslator = Traslator.create html

      # Add text annotations to the html 
      for annotationId, annotation of analysis.annotations 
        
        # If the annotation has no entity matches it could be a problem
        if annotation.entityMatches.length is 0
          $log.warn "Annotation #{annotation.text} [#{annotation.start}:#{annotation.end}] with id #{annotation.id} has no entity matches!"
          continue
        
        element = "<span id=\"#{annotationId}\" class=\"textannotation"
        
        # Loop annotation to see which has to be preselected
        for em in annotation.entityMatches
          entity = analysis.entities[ em.entityId ] 
          
          if annotationId in entity.occurrences
            # Preserve css classes for blind annotations
            
            if annotationId in entity.blindOccurrences
              element += " #{BLIND_ANNOTATION_CSS_CLASS}"
            
            # Mark the annotation as disambiguated    
            element += " disambiguated\" itemid=\"#{entity.id}"
        
        element += "\">"
              
        # Finally insert the HTML code.
        traslator.insertHtml element, text: annotation.start
        traslator.insertHtml '</span>', text: annotation.end

      # Add a zero-width no-break space after each annotation
      # to be sure that a caret container is available
      # See https://github.com/tinymce/tinymce/blob/master/js/tinymce/classes/Formatter.js#L2030
      html = traslator.getHtml()
      html = html.replace(/<\/span>/gim, "</span>#{INVISIBLE_CHAR}" )
      
      $rootScope.$broadcast "analysisEmbedded"
      # Update the editor Html code.
      isDirty = ed.isDirty()
      ed.setContent html, format: 'raw'
      ed.isNotDirty = not isDirty

  service
])