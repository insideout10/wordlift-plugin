class Traslator

  # Hold the html and textual positions.
  _htmlPositions: []
  _textPositions: []

# Hold the html and text contents.
  _html: ''
  _text: ''

  decodeHtml = (html)->
    txt = document.createElement("textarea")
    txt.innerHTML = html
    txt.value

  # Create an instance of the traslator.
  @create: (html) ->
    traslator = new Traslator(html)
    traslator.parse()
    traslator

  @version: '1.0.0'

  constructor: (html) ->
    @_html = html

  parse: ->
    @_htmlPositions = []
    @_textPositions = []
    @_text = ''

    # Changing this regex requires changing the regex also in WLS. Update the Traslator version when changing the regex.
    pattern = /([^&<>]*)(&[^&;]*;|<[!\/]?(?:[\w-]+|\[cdata\[.*?]])(?: [\w_-]+(?:="[^"]*")?)*>)([^&<>]*)/gim

    textLength = 0
    htmlLength = 0

    while (match = pattern.exec @_html)?

# Get the text pre/post and the html element
      htmlPre = match[1]
      htmlElem = match[2]
      htmlPost = match[3]

      # Get the text pre/post w/o new lines.
      # Add \n\n when it's needed depending on last tag
      textPre = htmlPre + (if htmlElem.toLowerCase() in ['</p>', '</li>'] then '\n\n' else '')
      #      dump "[ htmlPre length :: #{htmlPre.length} ][ textPre length :: #{textPre.length} ]"
      textPost = htmlPost

      # Sum the lengths to the existing lengths.
      textLength += textPre.length

      if /^&[^&;]*;$/gim.test htmlElem
        textLength += 1

      # For html add the length of the html element.
      htmlLength += htmlPre.length + htmlElem.length

      # Add the position.
      @_htmlPositions.push htmlLength
      @_textPositions.push textLength

      textLength += textPost.length
      htmlLength += htmlPost.length

      htmlProcessed = ''
      if /^&[^&;]*;$/gim.test htmlElem
        htmlProcessed = decodeHtml htmlElem

      # Add the textual parts to the text.
      @_text += textPre + htmlProcessed + textPost


    # In case the regex didn't find any tag, copy the html over the text.
    @_text = new String(@_html) if '' is @_text and !pattern.match @_html

    # Add text position 0 if it's not already set.
    if 0 is @_textPositions.length or 0 isnt @_textPositions[0]
      @_htmlPositions.unshift 0
      @_textPositions.unshift 0

#    console.log '=============================='
#    console.log @_html
#    console.log @_text
#    console.log @_htmlPositions
#    console.log @_textPositions
#    console.log '=============================='

# Get the html position, given a text position.
  text2html: (pos) ->
    htmlPos = 0
    textPos = 0

    for i in [0...@_textPositions.length]
      break if pos < @_textPositions[i]
      htmlPos = @_htmlPositions[i]
      textPos = @_textPositions[i]

    #    dump "#{htmlPos} + #{pos} - #{textPos}"
    htmlPos + pos - textPos

# Get the text position, given an html position.
  html2text: (pos) ->
#    dump @_htmlPositions
#    dump @_textPositions

# Return 0 if the specified html position is less than the first HTML position.
    return 0 if pos < @_htmlPositions[0]

    htmlPos = 0
    textPos = 0

    for i in [0...@_htmlPositions.length]
      break if pos < @_htmlPositions[i]
      htmlPos = @_htmlPositions[i]
      textPos = @_textPositions[i]

    #    console.log "#{textPos} + #{pos} - #{htmlPos}"
    textPos + pos - htmlPos

# Insert an Html fragment at the specified location.
  insertHtml: (fragment, pos) ->

#    dump @_htmlPositions
#    dump @_textPositions
#    console.log "[ fragment :: #{fragment} ][ pos text :: #{pos.text} ]"
    htmlPos = @text2html pos.text

    @_html = @_html.substring(0, htmlPos) + fragment + @_html.substring(htmlPos)

    # Reparse
    @parse()

# Return the html.
  getHtml: ->
    @_html

# Return the text.
  getText: ->
    @_text
window.Traslator = Traslator
angular.module('wordlift.utils.directives', [])
# See https://github.com/angular/angular.js/blob/master/src/ng/directive/ngEventDirs.js
.directive('wlOnError', ['$parse', '$window', '$log', ($parse, $window, $log)->
  restrict: 'A'
  compile: ($element, $attrs) ->  
    return (scope, element)->
      fn = $parse($attrs.wlOnError)
      element.on('error', (event)->
        callback = ()->
      	  fn(scope, { $event: event })
        scope.$apply(callback)
      )
])
.directive('wlFallback', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  priority: 99 # it needs to run after the attributes are interpolated
  link: ($scope, $element, $attrs, $ctrl) ->  
    $element.bind('error', ()->
      unless $attrs.src is $attrs.wlFallback
        $log.warn "Error on #{$attrs.src}! Going to fallback on #{$attrs.wlFallback}"
        $attrs.$set 'src', $attrs.wlFallback
    )
])
.directive('wlHideAfter', ['$timeout', '$log', ($timeout, $log)->
  restrict: 'A'
  link: ($scope, $element, $attrs, $ctrl) ->  
    delay = +$attrs.wlHideAfter
    $timeout(()->
      $log.debug "Remove msg after #{delay} ms"
      $element.hide()
    , delay)
])
.directive('wlClipboard', ['$timeout', '$document', '$log', ($timeout, $document, $log)->
  restrict: 'E'
  scope:
    text: '='
    onCopied: '&'
  transclude: true
  template: """
    <span 
      class="wl-widget-post-link" 
      ng-class="{'wl-widget-post-link-copied' : $copied}"
      ng-click="copyToClipboard()">
      <ng-transclude></ng-transclude>
      <input type="text" ng-value="text" />
    </span>
  """
  link: ($scope, $element, $attrs, $ctrl) ->  
    
    $scope.$copied = false

    $scope.node = $element.find 'input'
    $scope.node.css 'position', 'absolute'
    $scope.node.css 'left', '-10000px'
    
    # $element
    $scope.copyToClipboard = ()->
      try
        
        # Set inline style to override css styles
        $document[0].body.style.webkitUserSelect = 'initial'
        selection = $document[0].getSelection()
        selection.removeAllRanges()
        # Fake node selection
        $scope.node.select()
        # Perform the task
        unless $document[0].execCommand 'copy'
           $log.warn "Error on clipboard copy for #{text}"
        selection.removeAllRanges()
        # Update copied status and reset after 3 seconds
        $scope.$copied = true
        $timeout(()->
          $log.debug "Going to reset $copied status"
          $scope.$copied = false
        , 3000)

        # Execute onCopied callback
        if angular.isFunction($scope.onCopied)
          $scope.$evalAsync $scope.onCopied()
          
      finally
        $document[0].body.style.webkitUserSelect = ''
])
angular.module('wordlift.ui.carousel', ['ngTouch'])
.directive('wlCarousel', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  scope: true
  transclude: true      
  template: """
      <div class="wl-carousel" ng-class="{ 'active' : areControlsVisible }" ng-show="panes.length > 0" ng-mouseover="showControls()" ng-mouseleave="hideControls()">
        <div class="wl-panes" ng-style="{ width: panesWidth, left: position }" ng-transclude ng-swipe-left="next()" ng-swipe-right="prev()" ></div>
        <div class="wl-carousel-arrows" ng-show="areControlsVisible" ng-class="{ 'active' : isActive() }">
          <i class="wl-angle left" ng-click="prev()" ng-show="isPrevArrowVisible()" />
          <i class="wl-angle right" ng-click="next()" ng-show="isNextArrowVisible()" />
        </div>
      </div>
  """
  controller: [ '$scope', '$element', '$attrs', '$log', ($scope, $element, $attrs, $log) ->
      
    w = angular.element $window

    $scope.setItemWidth = ()->
      $element.width() / $scope.visibleElements() 

    $scope.showControls = ()->
      $scope.areControlsVisible = true

    $scope.hideControls = ()->
      $scope.areControlsVisible = false

    $scope.visibleElements = ()->
      if $element.width() > 460
        return 4
      return 1

    $scope.isActive = ()->
      $scope.isPrevArrowVisible() or $scope.isNextArrowVisible()
        
    $scope.isPrevArrowVisible = ()->
      ($scope.currentPaneIndex > 0)
    
    $scope.isNextArrowVisible = ()->
      ($scope.panes.length - $scope.currentPaneIndex) > $scope.visibleElements()
    
    $scope.next = ()->
      if ( $scope.currentPaneIndex + $scope.visibleElements() + 1 ) > $scope.panes.length
        return 
      $scope.position = $scope.position - $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex + 1

    $scope.prev = ()->
      if $scope.currentPaneIndex is 0
        return 
      $scope.position = $scope.position + $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex - 1
    
    $scope.setPanesWrapperWidth = ()->
      $scope.panesWidth = ( $scope.panes.length * $scope.itemWidth ) 
      $scope.position = 0;
      $scope.currentPaneIndex = 0

    $scope.itemWidth =  $scope.setItemWidth()
    $scope.panesWidth = undefined
    $scope.panes = []
    $scope.position = 0;
    $scope.currentPaneIndex = 0
    $scope.areControlsVisible = false

    w.bind 'resize', ()->
        
      $scope.itemWidth = $scope.setItemWidth();
      $scope.setPanesWrapperWidth()
      for pane in $scope.panes
        pane.scope.setWidth $scope.itemWidth
      $scope.$apply()

    ctrl = @
    ctrl.registerPane = (scope, element, first)->
      # Set the proper width for the element
      scope.setWidth $scope.itemWidth
        
      pane =
        'scope': scope
        'element': element

      $scope.panes.push pane
      $scope.setPanesWrapperWidth()
      
      #if first
      #  $log.debug "Eccolo"
      #  $log.debug $scope.panes.length
      #  $scope.position = $scope.panes.length * $scope.itemWidth
      #  $scope.currentPaneIndex = $scope.panes.length

    ctrl.unregisterPane = (scope)->
        
      unregisterPaneIndex = undefined
      for pane, index in $scope.panes
        if pane.scope.$id is scope.$id
          unregisterPaneIndex = index

      $scope.panes.splice unregisterPaneIndex, 1
      $scope.setPanesWrapperWidth()
  ]   
])
.directive('wlCarouselPane', ['$log', ($log)->
  require: '^wlCarousel'
  restrict: 'EA'
  scope:
    wlFirstPane: '='
  transclude: true 
  template: """
      <div ng-transclude></div>
  """
  link: ($scope, $element, $attrs, $ctrl) ->

    $element.addClass "wl-carousel-item"
    $scope.isFirst = $scope.wlFirstPane || false

    $scope.setWidth = (size)->
      $element.css('width', "#{size}px")

    $scope.$on '$destroy', ()->
      $log.debug "Destroy #{$scope.$id}"
      $ctrl.unregisterPane $scope

    $ctrl.registerPane $scope, $element, $scope.isFirst
])
angular.module('wordlift.editpost.widget.controllers.EditPostWidgetController', [
  'wordlift.editpost.widget.services.AnalysisService'
  'wordlift.editpost.widget.services.EditorService'
  'wordlift.editpost.widget.services.GeoLocationService'
  'wordlift.editpost.widget.providers.ConfigurationProvider'
])
.filter('filterEntitiesByTypesAndRelevance', [ 'configuration', '$log', (configuration, $log)->
  return (items, types)->

    filtered = []

    if not items?
      return filtered

    treshold = Math.floor ( (1/120) * Object.keys(items).length ) + 0.75

    for id, entity of items
      if  entity.mainType in types
        filtered.push entity

        #annotations_count = Object.keys( entity.annotations ).length
        #if annotations_count is 0
        #  continue

        #if annotations_count > treshold and entity.confidence is 1
        #  filtered.push entity
        #  continue
        #if entity.occurrences.length > 0
        #  filtered.push entity
        #  continue
        #if entity.id.startsWith configuration.datasetUri
        #  filtered.push entity

    filtered

])

.filter('filterTruncate', [ '$log', ($log)->

  return (input, words) ->
    if isNaN(words)
      return input
    if words <= 0
      return ''
    if input
      inputWords = input.split(/\s+/)
      if inputWords.length > words
        input = inputWords.slice(0, words).join(' ') + '…'
    input
])

.filter('filterSplitInRows', [ '$log', ($log)->
  return (arrayLength)->
    if arrayLength
      arrayLength = Math.ceil arrayLength
      arr = [0..(arrayLength-1)]
      arr
])
.filter('filterEntitiesByTypes', [ '$log', ($log)->
  return (items, types)->

    filtered = []

    for id, entity of items
      if  entity.mainType in types
        filtered.push entity
    filtered

])

.filter('isEntitySelected', [ '$log', ($log)->
  return (items)->

    filtered = []

    for id, entity of items
      if entity.occurrences.length > 0
        filtered.push entity

    filtered
])
.controller('EditPostWidgetController', [ 'GeoLocationService', 'RelatedPostDataRetrieverService', 'EditorService', 'AnalysisService', 'configuration', '$log', '$scope', '$rootScope', (GeoLocationService, RelatedPostDataRetrieverService, EditorService, AnalysisService, configuration, $log, $scope, $rootScope)->

  $scope.isRunning = false
  $scope.isGeolocationRunning = false

  $scope.analysis = undefined
  $scope.relatedPosts = undefined

  # A reference to the current entity
  $scope.currentEntity = undefined
  $scope.currentEntityType = undefined

  $scope.setCurrentEntity = (entity, entityType)->

    $scope.currentEntity = entity
    $scope.currentEntityType = entityType

    switch entityType
      when 'entity'
        $log.debug "An existing entity. Nothing to do"
      else # New entity

        $log.debug "A new entity"
        # Create a new entity
        $scope.currentEntity = AnalysisService.createEntity()

        if !$scope.isThereASelection and !$scope.annotation?
          $scope.addMsg 'Select a text or an existing annotation in order to create a new entity. Text selections are valid only if they do not overlap other existing annotation', 'error'
          $scope.unsetCurrentEntity()
          return
        if $scope.annotation?
          # Retrieve the current annotation
          annotation = $scope.analysis.annotations[ $scope.annotation ]
          # Set the entity label accordingly to the current annotation
          $scope.currentEntity.label = annotation.text
          return

        EditorService.createTextAnnotationFromCurrentSelection()


  $scope.unsetCurrentEntity = ()->
    $scope.currentEntity = undefined
    $scope.currentEntityType = undefined

  $scope.storeCurrentEntity = ()->

    unless $scope.currentEntity.mainType
      $scope.addMsg 'Please do not forgive to specify a type for this entity!', 'error'
      return

    switch $scope.currentEntityType
      when 'entity'
        $scope.analysis.entities[ $scope.currentEntity.id ] = $scope.currentEntity
        $scope.addMsg 'The entity was updated!', 'positive'

      else # New entity
        $log.debug 'Unset a new entity'
        $scope.addNewEntityToAnalysis()
        $scope.addMsg 'The entity was created!', 'positive'

    $scope.unsetCurrentEntity()

    # Trigger again the analysis results to have React update its tree
    wp.wordlift.trigger 'analysis.result', $scope.analysis

  $scope.selectedEntities = {}

  # A reference to the current section in the widget
  $scope.currentSection = undefined

  # Toggle the current section
  $scope.toggleCurrentSection = (section)->
    if $scope.currentSection is section
      $scope.currentSection = undefined
    else
      $scope.currentSection = section
  # Check current section
  $scope.isCurrentSection = (section)->
    $scope.currentSection is section

  $scope.suggestedPlaces = undefined
  $scope.publishedPlace = configuration.publishedPlace
  $scope.topic = undefined

  if configuration.publishedPlace?
    $scope.suggestedPlaces = {}
    $scope.suggestedPlaces[ configuration.publishedPlace.id ] = configuration.publishedPlace


  $scope.annotation = undefined
  $scope.boxes = []
  $scope.images = []

  $scope.isThereASelection = false
  $scope.configuration = configuration
  $scope.messages = []

  # Load related posts starting from local storage entities ids
  RelatedPostDataRetrieverService.load Object.keys( $scope.configuration.entities )

  $rootScope.$on "analysisFailed", (event, errorMsg) ->
    $scope.addMsg errorMsg, 'error'

  $rootScope.$on "analysisServiceStatusUpdated", (event, newStatus) ->
    $scope.isRunning = newStatus
    # When the analysis is running the editor is disabled and viceversa
    EditorService.updateContentEditableStatus !newStatus

  # Watch editor selection status
  $rootScope.$watch 'selectionStatus', ()->
    $scope.isThereASelection = $rootScope.selectionStatus

  for box in $scope.configuration.classificationBoxes
    $scope.selectedEntities[ box.id ] = {}

  $scope.removeMsg = (index)->
    $scope.messages.splice index, 1

  $scope.addMsg = (msg, level)->
    $scope.messages.unshift { level: level, text: msg }

  # Delegate to EditorService
  $scope.selectAnnotation = (annotationId)->
    EditorService.selectAnnotation annotationId

  $scope.hasAnalysis = ()->
    $scope.analysis?

  $scope.isEntitySelected = (entity, box)->
    return $scope.selectedEntities[ box.id ][ entity.id ]?

  $scope.isLinkedToCurrentAnnotation = (entity)->
    return ($scope.annotation in entity.occurrences)

  $scope.addNewEntityToAnalysis = ()->

    delete $scope.currentEntity.suggestedSameAs

    # Add new entity to the analysis
    $scope.analysis.entities[ $scope.currentEntity.id ] = $scope.currentEntity
    annotation = $scope.analysis.annotations[ $scope.annotation ]
    annotation.entityMatches.push { entityId: $scope.currentEntity.id, confidence: 1 }
    $scope.analysis.entities[ $scope.currentEntity.id ].annotations[ annotation.id ] = annotation
    $scope.analysis.annotations[ $scope.annotation ].entities[ $scope.currentEntity.id ] = $scope.currentEntity

    $scope.onSelectedEntityTile $scope.analysis.entities[ $scope.currentEntity.id ]

  $scope.$on "updateOccurencesForEntity", (event, entityId, occurrences) ->

    # $log.debug "Occurrences #{occurrences.length} for #{entityId}"
    $scope.analysis.entities[ entityId ].occurrences = occurrences

    # Ghost event to bridge React.
    wp.wordlift.trigger 'updateOccurrencesForEntity', { entityId: entityId, occurrences: occurrences }

    if occurrences.length is 0
      for box, entities of $scope.selectedEntities
        delete $scope.selectedEntities[ box ][ entityId ]

  # Observe current annotation changed
  # TODO la creazione di una nuova entità non andrebbe qui
  $scope.$watch "annotation", (newAnnotationId)->

    $log.debug "Current annotation id changed to #{newAnnotationId}"
    # Execute just once the analysis is properly performed
    return if $scope.isRunning
    # Execute just if the current annotation id is defined
    return unless newAnnotationId?
    # Execute just if any current entity ise set
    if $scope.currentEntity?
      # Retrieve the current annotation
      annotation = $scope.analysis.annotations[ newAnnotationId ]
      # Set the entity label accordingly to the current annotation
      $scope.currentEntity.label = annotation.text
      # Look for sameAs suggestions
      AnalysisService.getSuggestedSameAs annotation.text

  $scope.$on "textAnnotationClicked", (event, annotationId) ->
    $scope.annotation = annotationId
    $scope.unsetCurrentEntity()

  $scope.$on "textAnnotationAdded", (event, annotation) ->
    $log.debug "added a new annotation with Id #{annotation.id}"
    # Add the new annotation to the current analysis
    $scope.analysis.annotations[ annotation.id ] = annotation
    # Set the annotation scope
    $scope.annotation = annotation.id

  $scope.$on "sameAsRetrieved", (event, sameAs) ->
    $scope.currentEntity.suggestedSameAs = sameAs

  $scope.$on "relatedPostsLoaded", (event, posts) ->
    $scope.relatedPosts = posts

  $scope.$on "analysisPerformed", (event, analysis) ->
    $scope.analysis = analysis

    # Topic Preselect
    if $scope.configuration.topic?
      for topic in analysis.topics
        if topic.id in $scope.configuration.topic.sameAs
          $scope.topic = topic

    # Preselect
    for box in $scope.configuration.classificationBoxes
      for entityId in box.selectedEntities
        if entity = analysis.entities[ entityId ]

          if entity.occurrences.length is 0
            $log.warn "Entity #{entityId} selected as #{box.label} without valid occurences!"
            continue

          $scope.selectedEntities[ box.id ][ entityId ] = analysis.entities[ entityId ]
          # Concat entity images to suggested images collection
          for image in entity.images
            unless image in $scope.images
              $scope.images.push image

        else
          $log.warn "Entity with id #{entityId} should be linked to #{box.id} but is missing"
    # Open content classification box
    $scope.currentSection = 'content-classification'

  $scope.updateRelatedPosts = ()->
    $log.debug "Going to update related posts box ..."
    entityIds = []
    for box, entities of $scope.selectedEntities
      for id, entity of entities
        entityIds.push id
    RelatedPostDataRetrieverService.load entityIds

  $scope.onSelectedEntityTile = (entity)->

    # Detect if the entity has to be selected or unselected
    action = 'entitySelected'
    # If bottom / up disambiguation mode is on
    # and the current annotation is already included in occurrences collaction
    # then entity has to be deselected
    if $scope.annotation?
      if $scope.annotation in entity.occurrences
        action = 'entityDeselected'
    # If top / down disambiguation mode is on
    # and occurrences collection is not empty
    # then entity has to be deselected
    else
      if entity.occurrences.length > 0
        action = 'entityDeselected'

    scopeId = configuration.getCategoryForType entity.mainType
    $log.debug "Action '#{action}' on entity #{entity.id} within #{scopeId} scope"

    if action is 'entitySelected'
      # Ensure to mark the current entity to selected entities
      $scope.selectedEntities[ scopeId ][ entity.id ] = entity
      # Concat entity images to suggested images collection
      for image in entity.images
        unless image in $scope.images
          $scope.images.push image
    else
      # Remove current entity images from suggested images collection
      $scope.images = $scope.images.filter (img)->
        img not in entity.images

    # Notify to EditorService
    $scope.$emit action, entity, $scope.annotation

    # Trigger the action globally to enable React to receive the event.
    wp.wordlift.trigger action, { entity: entity, annotation: $scope.annotation }

    # Update related posts
    $scope.updateRelatedPosts()

    # Reset current annotation
    $scope.selectAnnotation undefined

  $scope.isGeoLocationAllowed = ()->
    GeoLocationService.isAllowed()

  $scope.getLocation = ()->
    $scope.isGeolocationRunning = true
    $rootScope.$broadcast 'geoLocationStatusUpdated', $scope.isGeolocationRunning
    GeoLocationService.getLocation()

  $scope.isPublishedPlace = (entity)->
    entity.id is $scope.publishedPlace?.id

  $scope.hasPublishedPlace = ()->
    $scope.publishedPlace? or $scope.suggestedPlaces?

  $scope.onPublishedPlaceSelected = (entity)->
    if $scope.publishedPlace?.id is entity.id
      $scope.publishedPlace = undefined
      $scope.suggestedPlaces = undefined
      return
    $scope.publishedPlace = entity

  $scope.$on "currentUserLocalityDetected", (event, match, locality) ->
    $log.debug "Looking for entities matching #{match} for locality #{locality}"

    AnalysisService._innerPerform match
    .then (response)->
      $scope.suggestedPlaces = {}
      for id, entity of response.data.entities
        # Evaluate similarity
        if 'place' is entity.mainType and locality is entity.label
          entity.id = id
          $scope.onPublishedPlaceSelected entity

      $scope.isGeolocationRunning = false
      $rootScope.$broadcast 'geoLocationStatusUpdated', $scope.isGeolocationRunning


  $scope.$on "geoLocationError", (event, msg) ->
    # Show error to the user
    $scope.addMsg "Sorry. Looks like something went wrong and WordLift cannot detect your current position. Make sure the ​location services​ of your browser are turned on.", 'error'
    # Stop geolocation loader
    $scope.isGeolocationRunning = false
    $rootScope.$broadcast 'geoLocationStatusUpdated', $scope.isGeolocationRunning

  $scope.isTopic = (topic)->
    topic.id is $scope.topic?.id
  $scope.onTopicSelected = (topic)->
    if $scope.topic?.id is topic.id
      $scope.topic = undefined
      return
    $scope.topic = topic

])
angular.module('wordlift.editpost.widget.directives.wlClassificationBox', [])
.directive('wlClassificationBox', ['configuration', '$log', (configuration, $log)->
    restrict: 'E'
    scope: true
    transclude: true
    templateUrl: ()->
      configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-classification-box.html'
    link: ($scope, $element, $attrs, $ctrl) ->

      $scope.hasSelectedEntities = ()->
        Object.keys( $scope.selectedEntities[ $scope.box.id ] ).length > 0

      wp.wordlift.trigger 'wlClassificationBox.loaded', $scope

    controller: ($scope, $element, $attrs) ->

      # Mantain a reference to nested entity tiles $scope
      # TODO manage on scope distruction event
      $scope.tiles = []

      $scope.boxes[ $scope.box.id ] = $scope

      ctrl = @
      ctrl.addTile = (tile)->
        $scope.tiles.push tile
      ctrl.closeTiles = ()->
        for tile in $scope.tiles
          tile.isOpened = false

])

# This directive is a small placeholder to have React load itself.
angular.module('wordlift.editpost.widget.directives.wlEntityList', [])
  .directive('wlEntityList', ['$log', ($log) ->

    restrict: 'A'

    # Trigger the event which will load the React application.
    link: () -> wp.wordlift.trigger 'wlEntityList.loaded'

])

angular.module('wordlift.editpost.widget.directives.wlEntityForm', [])
.directive('wlEntityForm', ['configuration', '$window', '$log', (configuration, $window, $log)->
    restrict: 'E'
    scope:
      entity: '='
      onSubmit: '&'
      onReset: '&'
      box: '='
    templateUrl: ()->
      configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-entity-form.html'

    link: ($scope, $element, $attrs, $ctrl) ->  

      $scope.configuration = configuration
      $scope.currentCategory = undefined

      $scope.$watch 'entity.id', (entityId)->
        if entityId?
          $log.debug "Entity updated to #{entityId}"
          category = configuration.getCategoryForType $scope.entity?.mainType
          $log.debug "Going to update current category to #{category}"
          $scope.currentCategory = category

      $scope.onSubmitWrapper = (e)->
        e.preventDefault()
        $scope.onSubmit()

      $scope.onResetWrapper = (e)->
        e.preventDefault()
        $scope.onReset()

      $scope.setCurrentCategory = (categoryId)->
        $scope.currentCategory = categoryId
        # If there is only one type related the new category
        # Type selection is forced
        types = configuration.getTypesForCategoryId( categoryId )
        $log.debug "Going to check types"
        $log.debug types
        if types.length is 1
          $scope.setType( types[0] )


      $scope.unsetCurrentCategory = ()->
        $scope.currentCategory = undefined 
        # Entity type has to be reset too        
        $scope.entity?.mainType = undefined

      $scope.isSameAsOf = (sameAs)->
        sameAs.id in $scope.entity.sameAs
      
      $scope.addSameAs = (sameAs)->
        
        unless $scope.entity?.sameAs
          $scope.entity?.sameAs = []
        
        if sameAs.id in $scope.entity.sameAs 
          index = $scope.entity.sameAs.indexOf sameAs.id
          $scope.entity.sameAs.splice index, 1
        else
          $scope.entity?.sameAs.push sameAs.id
      
      $scope.setType = (entityType)->
        return if entityType is $scope.entity?.mainType
        $scope.entity?.mainType = entityType
      
      $scope.isCurrentType = (entityType)->
        return $scope.entity?.mainType is entityType
        
      $scope.getAvailableTypes = ()->
        return configuration.getTypesForCategoryId $scope.currentCategory

      $scope.removeCurrentImage = (index)->
        removed = $scope.entity.images.splice index, 1
        $log.warn "Removed #{removed} from entity #{$scope.entity.id} images collection"

      $scope.linkToEdit = (e)->
        e.preventDefault()
        $window.location.href = ajaxurl + '?action=wordlift_redirect&uri=' + $window.encodeURIComponent($scope.entity.id) + "&to=edit"

      $scope.hasOccurences = ()->
        $scope.entity.occurrences?.length > 0
      
      $scope.setSameAs = (uri)->
        $scope.entity.sameAs = uri

      $scope.isInternal = ()->
        configuration.isInternal $scope.entity?.id 

      $scope.isNew = (uri)->
        return !/^(f|ht)tps?:\/\//i.test $scope.entity?.id 

])

angular.module('wordlift.editpost.widget.directives.wlEntityTile', [])
.directive('wlEntityTile', [ 'configuration','$log', (configuration, $log)->
    require: '?^wlClassificationBox'
    restrict: 'E'
    scope:
      entity: '='
      isSelected: '='
      showConfidence: '='
      onSelect: '&'
      onMore: '&'
    templateUrl: ()->
      configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-entity-tile.html'
    link: ($scope, $element, $attrs, $boxCtrl) ->
      
      $scope.configuration = configuration
      # Add tile to related container scope
      $boxCtrl?.addTile $scope

      $scope.isOpened = false

      $scope.isInternal = ()->
        if $scope.entity.id.startsWith configuration.datasetUri
          return true
        return false

      $scope.toggle = ()->
        if !$scope.isOpened
          $boxCtrl?.closeTiles()
        $scope.isOpened = !$scope.isOpened

  ])

angular.module('wordlift.editpost.widget.directives.wlEntityInputBox', [])
# The wlEntityInputBoxes prints the inputs and textareas with entities data.
.directive('wlEntityInputBox', ['configuration', '$log', (configuration, $log)->
    restrict: 'E'
    scope:
      entity: '='
    templateUrl: ()->
      configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-entity-input-box.html'
])
angular.module('wordlift.editpost.widget.services.EditorAdapter', [
  'wordlift.editpost.widget.services.EditorAdapter'
])
# An adapter to the page editor.
#
# @since 1.4.2
.service('EditorAdapter', ['$log', ($log)->

  # Create the service definition.
  service =

    # Get the editor with the specified id (by default 'content').
    #
    # @since 1.4.2
    #
    # @param string id The editor's id (by default 'content').
    # @return The editor instance.
    getEditor: (id = 'content') ->
      tinyMCE.get(id)

    # Get the HTML code in the specified editor (by default 'content').
    #
    # @since 1.4.2
    #
    # @param string id The editor's id (by default 'content').
    # @return The editor's HTML content.
    getHTML: (id) ->
      service.getEditor(id).getContent format: 'raw'

  # Finally return the service.
  service
])
angular.module('wordlift.editpost.widget.services.AnnotationParser', [])
# Parse annotations.
#
# @since 1.4.2
.service('AnnotationParser', ['$log', ($log)->

  # Define the service.
  service =

    # Parse the provided HTML code for annotations.
    #
    # @since 1.4.2
    #
    # @param string html The HTML code to parse.
    # @return array An array of annotations.
    parse: (html) ->

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

        annotation
      )

  # Finally return the service.
  service
])
angular.module('wordlift.editpost.widget.services.AnalysisService', [
  'wordlift.editpost.widget.services.AnnotationParser',
  'wordlift.editpost.widget.services.EditorAdapter',
])
# Manage redlink analysis responses
# @since 1.0.0
  .service('AnalysisService', ['AnnotationParser', 'EditorAdapter', 'configuration', '$log', '$http', '$rootScope',
  (AnnotationParser, EditorAdapter, configuration, $log, $http, $rootScope)->

# Creates a unique ID of the specified length (default 8).
    uniqueId = (length = 8) ->
      id = ''
      id += Math.random().toString(36).substr(2) while id.length < length
      id.substr 0, length

    # Merges two objects by copying overrides param onto the options.
    merge = (options, overrides) ->
      extend (extend {}, options), overrides
    extend = (object, properties) ->
      for key, val of properties
        object[key] = val
      object

    findAnnotation = (annotations, start, end) ->
      return annotation for id, annotation of annotations when annotation.start is start and annotation.end is end

    service =
      _isRunning: false
      _currentAnalysis: undefined
      _supportedTypes: []
      _defaultType: "thing"

    service.cleanAnnotations = (analysis, positions = []) ->
# Take existing entities as mandatory
      for annotationId, annotation of analysis.annotations
        if annotation.start > 0 and annotation.end > annotation.start
          annotationRange = [ annotation.start..annotation.end ]
          # TODO Replace with an Array intersection check
          isOverlapping = false
          for pos in annotationRange
            if pos in positions
              isOverlapping = true
            break

          if isOverlapping
            $log.warn "Annotation with id: #{annotationId} start: #{annotation.start} end: #{annotation.end} overlaps an existing annotation"
            @.deleteAnnotation analysis, annotationId
          else
            positions = positions.concat annotationRange

      return analysis

    # Retrieve supported type from current classification boxes configuration
    for box in configuration.classificationBoxes
      for type in box.registeredTypes
        if type not in service._supportedTypes
          service._supportedTypes.push type

    service.createEntity = (params = {}) ->
# Set the defalut values.
      defaults =
        id: 'local-entity-' + uniqueId 32
        label: ''
        description: ''
        mainType: '' # No DefaultType
        types: []
        images: []
        confidence: 1
        occurrences: []
        annotations: {}

      merge defaults, params

    # Delete an annotation from a given analyis and an annotationId
    service.deleteAnnotation = (analysis, annotationId)->
      $log.warn "Going to remove overlapping annotation with id #{annotationId}"

      if analysis.annotations[annotationId]?
        for ea, index in analysis.annotations[annotationId].entityMatches
          delete analysis.entities[ea.entityId].annotations[annotationId]
        delete analysis.annotations[annotationId]

      analysis

    service.createAnnotation = (params = {}) ->
# Set the defalut values.
      defaults =
        id: 'urn:local-text-annotation-' + uniqueId 32
        text: ''
        start: 0
        end: 0
        entities: []
        entityMatches: []

      merge defaults, params

    service.parse = (data) ->

# Add local entities
# Add id to entity obj
# Add id to annotation obj
# Add occurences as a blank array
# Add annotation references to each entity

# TMP ... Should be done on WLS side
#      unless data.topics?
#        data.topics = []
      dt = @._defaultType

      data.topics = data.topics.map (topic)->
        topic.id = topic.uri
        topic.occurrences = []
        topic.mainType = dt
        topic

      for id, localEntity of configuration.entities

        data.entities[id] = localEntity

      for id, entity of data.entities

# Remove the current entity from the proposed entities.
#
# See https://github.com/insideout10/wordlift-plugin/issues/437
# See https://github.com/insideout10/wordlift-plugin/issues/345
        if configuration.currentPostUri is id
          delete data.entities[id]
          continue

        if not entity.label
          $log.warn "Label missing for entity #{id}"

        if not entity.description
          $log.warn "Description missing for entity #{id}"

        if not entity.sameAs
          $log.warn "sameAs missing for entity #{id}"
          entity.sameAs = []
          configuration.entities[id]?.sameAs = []
          $log.debug "Schema.org sameAs overridden for entity #{id}"

        if entity.mainType not in @._supportedTypes
          $log.warn "Schema.org type #{entity.mainType} for entity #{id} is not supported from current classification boxes configuration"
          entity.mainType = @._defaultType
          configuration.entities[id]?.mainType = @._defaultType
          $log.debug "Schema.org type overridden for entity #{id}"

        entity.id = id
        entity.occurrences = []
        entity.annotations = {}
        entity.confidence = 1

      for id, annotation of data.annotations
        annotation.id = id
        annotation.entities = {}

        # Filter out annotations that don't have a corresponding entity. The entities list might be filtered, in order
        # to remove the local entity.
        data.annotations[id].entityMatches = (ea for ea in annotation.entityMatches when ea.entityId of data.entities)

        # Remove the annotation if there's no entity matches left.
        #
        # See https://github.com/insideout10/wordlift-plugin/issues/437
        # See https://github.com/insideout10/wordlift-plugin/issues/345
        if 0 is data.annotations[id].entityMatches.length
          delete data.annotations[id]
          continue

        for ea, index in data.annotations[id].entityMatches

          if not data.entities[ea.entityId].label
            data.entities[ea.entityId].label = annotation.text
            $log.debug "Missing label retrieved from related annotation for entity #{ea.entityId}"

          data.entities[ea.entityId].annotations[id] = annotation
          data.annotations[id].entities[ea.entityId] = data.entities[ea.entityId]

      # TODO move this calculation on the server
      for id, entity of data.entities
        for annotationId, annotation of data.annotations
          local_confidence = 1
          for em in annotation.entityMatches
            if em.entityId? and em.entityId is id
              local_confidence = em.confidence
          entity.confidence = entity.confidence * local_confidence

      data

    service.getSuggestedSameAs = (content)->
      promise = @._innerPerform content
# If successful, broadcast an *sameAsReceived* event.
        .then (response) ->
      suggestions = []

      for id, entity of response.data.entities

        if matches = id.match /^https?\:\/\/([^\/?#]+)(?:[\/?#]|$)/i
          suggestions.push {
            id: id
            label: entity.label
            mainType: entity.mainType
            source: matches[1]
          }
      $log.debug suggestions
      $rootScope.$broadcast "sameAsRetrieved", suggestions

    service._innerPerform = (content, annotations = [])->
      args =
        method: 'post'
        url: ajaxurl + '?action=wordlift_analyze'

      # Set the data as two parameters, content and annotations.
      args.headers = {'Content-Type': 'application/json'}
      args.data = {content: content, annotations: annotations, contentType: 'text/html', version: Traslator.version}

      if (wlSettings?)
        if (wlSettings.language?) then args.data.contentLanguage = wlSettings.language
        # We set the current entity URI as exclude from the analysis results.
        #
        # See https://github.com/insideout10/wordlift-plugin/issues/345
        if (wlSettings.itemId?) then args.data.exclude = [wlSettings.itemId]

      return $http(args)

    service._updateStatus = (status)->
      service._isRunning = status
      $rootScope.$broadcast "analysisServiceStatusUpdated", status

    service.perform = (content)->
      if service._currentAnalysis
        $log.warn "Analysis already run! Nothing to do ..."
        service._updateStatus false

        return

      service._updateStatus true

      # Get the existing annotations in the text.
      annotations = AnnotationParser.parse(EditorAdapter.getHTML())

      $log.debug 'Requesting analysis...'

      promise = @._innerPerform content, annotations
      # If successful, broadcast an *analysisPerformed* event.
      promise.then (response) ->
# Store current analysis obj
        service._currentAnalysis = response.data

        result = service.parse(response.data)
        $rootScope.$broadcast "analysisPerformed", result
        wp.wordlift.trigger 'analysis.result', result


      # On failure, broadcast an *analysisFailed* event.
      promise.catch (response) ->
        $log.error response.data
        $rootScope.$broadcast "analysisFailed", response.data

      # Update service running status in each case
      promise.finally (response) ->
        service._updateStatus false

    # Preselect entity annotations in the provided analysis using the provided collection of annotations.
    service.preselect = (analysis, annotations) ->
      $log.debug "Selecting entity annotations (#{annotations.length})..."

      # Find the existing entities in the html
      for annotation in annotations

        if annotation.start is annotation.end
          $log.warn "There is a broken empty annotation for entityId #{annotation.uri}"
          continue

        # Find the proper annotation
        textAnnotation = findAnnotation analysis.annotations, annotation.start, annotation.end

        # If there is no textAnnotation then create it and add to the current analysis
        # It can be normal for new entities that are queued for Redlink re-indexing
        if not textAnnotation?

          $log.warn "Text annotation #{annotation.start}:#{annotation.end} for entityId #{annotation.uri} misses in the analysis"

          textAnnotation = @createAnnotation({
            start: annotation.start
            end: annotation.end
            text: annotation.label
            # The css class of the original text annotation (now removed from the
            # body. The css class is useful because we store there the `wl-no-link`
            # class.
            cssClass: annotation.cssClass if annotation.cssClass?
          })
          analysis.annotations[textAnnotation.id] = textAnnotation

        # Look for the entity in the current analysis result
        # Local entities are merged previously during the analysis parsing
        entity = analysis.entities[annotation.uri]
        for id, e of configuration.entities
          entity = analysis.entities[e.id] if annotation.uri in e.sameAs

        # If no entity is found we have a problem
        if not entity?
          $log.warn "Entity with uri #{annotation.uri} is missing both in analysis results and in local storage"
          continue
        # Enhance analysis accordingly
        analysis.entities[entity.id].occurrences.push textAnnotation.id
        if not analysis.entities[entity.id].annotations[textAnnotation.id]?
          analysis.entities[entity.id].annotations[textAnnotation.id] = textAnnotation
          analysis.annotations[textAnnotation.id].entityMatches.push {entityId: entity.id, confidence: 1}
          analysis.annotations[textAnnotation.id].entities[entity.id] = analysis.entities[entity.id]

    service

])
# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.editpost.widget.services.EditorService', [
  'wordlift.editpost.widget.services.EditorAdapter',
  'wordlift.editpost.widget.services.AnalysisService'
  ])
# Manage redlink analysis responses
.service('EditorService', [ 'configuration', 'AnalysisService', 'EditorAdapter', '$log', '$http', '$rootScope', (configuration, AnalysisService, EditorAdapter, $log, $http, $rootScope)->

  INVISIBLE_CHAR = '\uFEFF'

  # Find existing entities selected in the html content (by looking for *itemid* attributes).
  findEntities = (html) ->
    # Prepare a traslator instance that will traslate Html and Text positions.
    traslator = Traslator.create html
    # Set the pattern to look for *itemid* attributes.
    pattern = /<(\w+)[^>]*\sclass="([^"]+)"\sitemid="([^"]+)"[^>]*>([^<]*)<\/\1>/gim

    # Get the matches and return them.
    (while match = pattern.exec html

      annotation =
        start: traslator.html2text match.index
        end: traslator.html2text (match.index + match[0].length)
        uri: match[3]
        label: match[4]
        cssClass: match[2]

      annotation
    )

  findPositions = ( entities ) ->
    positions = []
    for entityAnnotation in entities
      positions = positions.concat [ entityAnnotation.start..entityAnnotation.end ]
    positions

  # @deprecated use EditorAdapter.getEditor()
  editor = ->
    tinyMCE.get('content')

  disambiguate = ( annotationId, entity )->
    ed = EditorAdapter.getEditor()
    ed.dom.addClass annotationId, "disambiguated"
    for type in configuration.types
      ed.dom.removeClass annotationId, type.css
    ed.dom.removeClass annotationId, "unlinked"
    ed.dom.addClass annotationId, "wl-#{entity.mainType}"
    discardedItemId = ed.dom.getAttrib annotationId, "itemid"
    ed.dom.setAttrib annotationId, "itemid", entity.id
    discardedItemId

  dedisambiguate = ( annotationId, entity )->
    ed = EditorAdapter.getEditor()
    ed.dom.removeClass annotationId, "disambiguated"
    ed.dom.removeClass annotationId, "wl-#{entity.mainType}"
    discardedItemId = ed.dom.getAttrib annotationId, "itemid"
    ed.dom.setAttrib annotationId, "itemid", ""
    discardedItemId

  # TODO refactoring with regex
  currentOccurencesForEntity = (entityId) ->
    ed = EditorAdapter.getEditor()
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

  service =
    # Detect if there is a current selection
    hasSelection: ()->
      # A reference to the editor.
      ed = EditorAdapter.getEditor()
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
      ed = EditorAdapter.getEditor()
      ed.id is editor.id

    # Update contenteditable status for the editor
    updateContentEditableStatus: (status)->
      # A reference to the editor.
      ed = EditorAdapter.getEditor()
      ed.getBody().setAttribute 'contenteditable', status

    # Create a textAnnotation starting from the current selection
    createTextAnnotationFromCurrentSelection: ()->
      # A reference to the editor.
      ed = EditorAdapter.getEditor()
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
      content = EditorAdapter.getHTML() # ed.getContent format: 'raw'
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
      ed = EditorAdapter.getEditor()
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
      ed = EditorAdapter.getEditor()

      # Get the TinyMCE editor html content.
      html = EditorAdapter.getHTML() # ed.getContent format: 'raw'

      # Find existing entities.
      entities = findEntities html

      # Remove overlapping annotations preserving selected entities
      AnalysisService.cleanAnnotations analysis, findPositions(entities)

      # Preselect entities found in html. We also keep track of the original
      # text annotation css classes which may turn useful when checking additional
      # classes added to the text annotation, for example the `wl-no-link` css
      # class which we use to decide whether to activate or not a link.
      # We need to keep track now of the css classes because in a while we're
      # going to remove the text annotations and put them back.
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

        # Add the `wl-no-link` class if it was present in the original annotation.
        element += ' wl-no-link' if -1 < annotation.cssClass?.indexOf('wl-no-link')

        # Loop annotation to see which has to be preselected
        for em in annotation.entityMatches
          entity = analysis.entities[ em.entityId ]

          if annotationId in entity.occurrences
            element += " disambiguated wl-#{entity.mainType}\" itemid=\"#{entity.id}"

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
angular.module('wordlift.editpost.widget.services.RelatedPostDataRetrieverService', [])
# Manage redlink analysis responses
.service('RelatedPostDataRetrieverService', [ 'configuration', '$log', '$http', '$rootScope', (configuration, $log, $http, $rootScope)-> 
  
  service = {}
  service.load = ( entityIds = [] )->
    uri = "admin-ajax.php?action=wordlift_related_posts&post_id=#{configuration.currentPostId}"
    
    $http(
      method: 'post'
      url: uri
      data: entityIds
    )
    # If successful, broadcast an *analysisReceived* event.
    .success (data) ->
      $rootScope.$broadcast "relatedPostsLoaded", data
    .error (data, status) ->
      $log.warn "Error loading related posts"

  service

])
angular.module('wordlift.editpost.widget.services.GeoLocationService', ['geolocation'])
# Retrieve GeoLocation coordinates and process them trough reverse geocoding
.service('GeoLocationService', [ 'configuration', 'geolocation', '$log', '$rootScope', '$document', '$q', '$timeout', '$window', ( configuration, geolocation, $log, $rootScope, $document, $q, $timeout, $window)-> 
  
  GOOGLE_MAPS_LEVEL = 'locality'
  GOOGLE_MAPS_KEY = 'AIzaSyAhsajbqNVd7ABlkZvskWIPdiX6M3OaaNM'
  GOOGLE_MAPS_API_ENDPOINT = "https://maps.googleapis.com/maps/api/js?language=#{configuration.currentLanguage}&key=#{GOOGLE_MAPS_KEY}"
  
  $rootScope.$on 'error', (event, msg)->
    $log.warn "Geolocation error: #{msg}"
    $rootScope.$broadcast 'geoLocationError', msg

  # Following code is inspired by
  # https://github.com/urish/angular-load/blob/master/angular-load.js

  @googleApiLoaded = false
  @googleApiPromise = undefined

  loadGoogleAPI = ()->

    if @googleApiPromise?
      return @googleApiPromise

    deferred = $q.defer()
    # Load Google API asynchronously
    element = $document[0].createElement('script')
    # $log.debug "Going to load #{GOOGLE_MAPS_API_ENDPOINT}"
    element.src = GOOGLE_MAPS_API_ENDPOINT
    $document[0].body.appendChild element
    

    callback = (e) ->  
      if element.readyState and element.readyState not in ['complete', 'loaded'] 
        return
      
      $timeout(()->
        deferred.resolve(e)
      )

    element.onload = callback
    element.onreadystatechange = callback
    element.onerror = (e)->

      $timeout(()-> 
        deferred.reject(e)
      )

    @googleApiPromise = deferred.promise
    @googleApiPromise

  # Detect Current Browser
  currentBrowser = ()->
    userAgent = $window.navigator.userAgent
    browsers = 
      chrome: /chrome/i
      safari: /safari/i
      firefox: /firefox/i
      ie: /internet explorer/i
    for key of browsers
      if browsers[key].test(userAgent)
        return key
    'unknown'

  service = {}
  
  # Used to temporaly manage this scenario 
  # https://developers.google.com/web/updates/2016/04/geolocation-on-secure-contexts-only?hl=en
  service.isAllowed = ()->
    # $log.debug "Current browser #{currentBrowser()}, current protocol: #{$window.location.protocol}"
    if currentBrowser() is 'chrome'
      return $window.location.protocol is 'https:'
    true
    
  service.getLocation = ()->

    geolocation.getLocation()
    .then (data) ->
      
      $log.debug "Detected position: latitude #{data.coords.latitude}, longitude #{data.coords.longitude}"
      loadGoogleAPI()
      .then ()->

        geocoder = new google.maps.Geocoder()
        # Perform reverse geocode
        geocoder.geocode
          'location':
             'lat': data.coords.latitude
             'lng': data.coords.longitude
          , (results, status)->
            
            if status is google.maps.GeocoderStatus.OK
              for result in results
                if GOOGLE_MAPS_LEVEL in result.types
                  for ac in result.address_components
                    if GOOGLE_MAPS_LEVEL in ac.types
                      $rootScope.$broadcast "currentUserLocalityDetected", result.formatted_address, ac.long_name                                   
                      return    
             
  service

])


angular.module('wordlift.editpost.widget.providers.ConfigurationProvider', [])
.provider("configuration", ()->
  
  _configuration = undefined
  
  provider =
    setConfiguration: (configuration)->
      _configuration = configuration

      # Add utilities methods to work classification boxes

      # Return the proper category for a given entity type
      _configuration.getCategoryForType = (entityType)->

      	unless entityType
      	  return undefined
      	for category in @classificationBoxes 
      	  if entityType in category.registeredTypes
      	    return category.id 
      
      # Return registered types for a given category
      _configuration.getTypesForCategoryId = (categoryId)->
      	
      	unless categoryId
      	  return []
      	for category in @classificationBoxes 
      	  if categoryId is category.id 
      	  	return category.registeredTypes
      
      # Check if a given entity id refers to an internal entity
      _configuration.isInternal = (uri)->
      	return uri?.startsWith @datasetUri
      
      # Check if a given entity id refers to an internal entity
      _configuration.getUriForType = (mainType)->
        for type in @types
          if type.css is "wl-#{mainType}"
            return type.uri

      	    
    $get: ()->
      _configuration

  provider
)



# Set the well-known $ reference to jQuery.
$ = jQuery

# Create the main AngularJS module, and set it dependent on controllers and directives.
angular.module('wordlift.editpost.widget', [
  'ngAnimate'
  'wordlift.ui.carousel'
  'wordlift.utils.directives'
  'wordlift.editpost.widget.providers.ConfigurationProvider',
  'wordlift.editpost.widget.controllers.EditPostWidgetController',
  'wordlift.editpost.widget.directives.wlClassificationBox',
  'wordlift.editpost.widget.directives.wlEntityList',
  'wordlift.editpost.widget.directives.wlEntityForm',
  'wordlift.editpost.widget.directives.wlEntityTile',
  'wordlift.editpost.widget.directives.wlEntityInputBox',
  'wordlift.editpost.widget.services.AnalysisService',
  'wordlift.editpost.widget.services.EditorService',
  'wordlift.editpost.widget.services.RelatedPostDataRetrieverService'
])

.config((configurationProvider)->
  configurationProvider.setConfiguration window.wordlift
)

$(
  container = $("""
  	<div
      id="wordlift-edit-post-wrapper"
      ng-controller="EditPostWidgetController"
      ng-include="configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-editpost-widget.html'">
    </div>
  """)
  .appendTo('#wordlift-edit-post-outer-wrapper')

  # Add svg based spinner code
  spinner = $("""
    <div class="wl-widget-spinner">
      <svg transform-origin="10 10" id="wl-widget-spinner-blogger">
        <circle cx="10" cy="10" r="6" class="wl-blogger-shape"></circle>
      </svg>
      <svg transform-origin="10 10" id="wl-widget-spinner-editorial">
        <rect x="4" y="4" width="12" height="12" class="wl-editorial-shape"></rect>
      </svg>
      <svg transform-origin="10 10" id="wl-widget-spinner-enterprise">
        <polygon points="3,10 6.5,4 13.4,4 16.9,10 13.4,16 6.5,16" class="wl-enterprise-shape"></polygon>
      </svg>
    </div> 
  """)
  .appendTo('#wordlift_entities_box .ui-sortable-handle')

  injector = angular.bootstrap $('#wordlift-edit-post-wrapper'), ['wordlift.editpost.widget']

  # Update spinner
  injector.invoke(['$rootScope', '$log', ($rootScope, $log) ->
    $rootScope.$on 'analysisServiceStatusUpdated', (event, status) ->
      css = if status then 'wl-spinner-running' else ''
      $('.wl-widget-spinner svg').attr 'class', css

    $rootScope.$on 'geoLocationStatusUpdated', (event, status) ->
      css = if status then 'wl-spinner-running' else ''
      $('.wl-widget-spinner svg').attr 'class', css
  ])

  # Add WordLift as a plugin of the TinyMCE editor.
  tinymce.PluginManager.add 'wordlift', (editor, url) ->

    # This plugin has to be loaded only with the main WP "content" editor
    return unless editor.id is "content"

    # Register event depending on tinymce major version
    fireEvent = (editor, eventName, callback)->
      switch tinymce.majorVersion
        when '4' then editor.on eventName, callback
        when '3' then editor["on#{eventName}"].add callback

    injector.invoke(['EditorService', '$rootScope', '$log', (EditorService, $rootScope, $log) ->

      # Override wp.autosave.server.postChanged method
      # in order to avoid unexpected warning to the user
      if wp.autosave?
        wp.autosave.server.postChanged = ()->
          return false

      # Hack wp.mce.views to prevent shorcodes rendering
      # starts before the analysis is properly embedded
      # wp.mce.views uses toViews() method from WP 3.8 to 4.1
      # and setMarkers() method from WP 4.2 to 4.3 to replace
      # available shortcodes with coresponding views markup
      for method in ['setMarkers', 'toViews']
        if wp.mce.views[method]?

          originalMethod = wp.mce.views[method]
          $log.warn "Override wp.mce.views method #{method}() to prevent shortcodes rendering"
          wp.mce.views[method] = (content)->
            return content

          $rootScope.$on "analysisEmbedded", (event) ->
            $log.info "Going to restore wp.mce.views method #{method}()"
            wp.mce.views[method] = originalMethod

          $rootScope.$on "analysisFailed", (event) ->
            $log.info "Going to restore wp.mce.views method #{method}()"
            wp.mce.views[method] = originalMethod

          break
    ])

    # Perform analysis once tinymce is loaded
    fireEvent(editor, "LoadContent", (e) ->
      injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log'
        (AnalysisService, EditorService, $rootScope, $log) ->
          # execute the following commands in the angular js context.
          $rootScope.$apply(->
            # Get the html content of the editor.
            html = editor.getContent format: 'raw'

            if "" isnt html
              EditorService.updateContentEditableStatus false
              AnalysisService.perform html
            # Get the text content from the Html.
#            text = Traslator.create(html).getText()
#            if text.match /[a-zA-Z0-9]+/
#              # Disable tinymce editing
#              EditorService.updateContentEditableStatus false
#              AnalysisService.perform html
#            else
#              $log.warn "Blank content: nothing to do!"
          )
      ])
    )

    # Fires when the user changes node location using the mouse or keyboard in the TinyMCE editor.
    fireEvent(editor, "NodeChange", (e) ->
      injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log',
        (AnalysisService, EditorService, $rootScope, $log) ->
          if AnalysisService._currentAnalysis
            $rootScope.$apply(->
              $rootScope.selectionStatus = EditorService.hasSelection()
            )
          true

      ])
    )

    # this event is raised when a textannotation is selected in the TinyMCE editor.
    fireEvent(editor, "Click", (e) ->
      injector.invoke(['AnalysisService', 'EditorService', '$rootScope', '$log',
        (AnalysisService, EditorService, $rootScope, $log) ->
          if AnalysisService._currentAnalysis
            $rootScope.$apply(->
              EditorService.selectAnnotation e.target.id
            )
          true

      ])
    )

)
