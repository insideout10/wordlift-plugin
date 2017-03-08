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