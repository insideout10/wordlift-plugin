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
              
        annotations_count = Object.keys( entity.annotations ).length
        if annotations_count is 0
          continue

        if annotations_count > treshold and entity.confidence is 1
          filtered.push entity
          continue
        if entity.occurrences.length > 0
          filtered.push entity
          continue
        if entity.id.startsWith configuration.datasetUri
          filtered.push entity
        
        # TODO se è una entità di wordlift la mostro

    filtered

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
.controller('EditPostWidgetController', [ 'GeoLocationService', 'RelatedPostDataRetrieverService', 'EditorService', 'AnalysisService', 'configuration', '$log', '$scope', '$rootScope', '$compile', (GeoLocationService, RelatedPostDataRetrieverService, EditorService, AnalysisService, configuration, $log, $scope, $rootScope, $compile)-> 

  $scope.isRunning = false
  $scope.isGeolocationRunning = false

  $scope.analysis = undefined
  $scope.relatedPosts = undefined
  $scope.newEntity = AnalysisService.createEntity()
  $scope.selectedEntities = {}
    
  # TMP
  $scope.copiedOnClipboard = ()->
    $log.debug "Something copied on clipboard"

  # A reference to the current suggested image in the widget
  $scope.currentImage = undefined
  # Set the current image
  $scope.setCurrentImage = (image)->
    $scope.currentImage = image

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
  $scope.errors = []
  
  # Load related posts starting from local storage entities ids
  RelatedPostDataRetrieverService.load Object.keys( $scope.configuration.entities )

  $rootScope.$on "analysisFailed", (event, errorMsg) ->
    $scope.addError errorMsg

  $rootScope.$on "analysisServiceStatusUpdated", (event, newStatus) ->
    $scope.isRunning = newStatus
    # When the analysis is running the editor is disabled and viceversa
    EditorService.updateContentEditableStatus !newStatus

  # Watch editor selection status
  $rootScope.$watch 'selectionStatus', ()->
    $scope.isThereASelection = $rootScope.selectionStatus

  for box in $scope.configuration.classificationBoxes
    $scope.selectedEntities[ box.id ] = {}
          
  $scope.addError = (errorMsg)->
    $scope.errors.unshift { type: 'error', msg: errorMsg } 

  # Delegate to EditorService
  $scope.createTextAnnotationFromCurrentSelection = ()->
    EditorService.createTextAnnotationFromCurrentSelection()
  # Delegate to EditorService
  $scope.selectAnnotation = (annotationId)->
    EditorService.selectAnnotation annotationId

  $scope.hasAnalysis = ()->
    $scope.analysis? 

  $scope.isEntitySelected = (entity, box)->
    return $scope.selectedEntities[ box.id ][ entity.id ]?
  $scope.isLinkedToCurrentAnnotation = (entity)->
    return ($scope.annotation in entity.occurrences)

  $scope.addNewEntityToAnalysis = (scope)->
    
    if $scope.newEntity.sameAs
      $scope.newEntity.sameAs = [ $scope.newEntity.sameAs ]
    
    delete $scope.newEntity.suggestedSameAs
    
    # Add new entity to the analysis
    $scope.analysis.entities[ $scope.newEntity.id ] = $scope.newEntity
    annotation = $scope.analysis.annotations[ $scope.annotation ]
    annotation.entityMatches.push { entityId: $scope.newEntity.id, confidence: 1 }
    $scope.analysis.entities[ $scope.newEntity.id ].annotations[ annotation.id ] = annotation
    $scope.analysis.annotations[ $scope.annotation ].entities[ $scope.newEntity.id ] = $scope.newEntity
    
    # Select the new entity
    $scope.onSelectedEntityTile $scope.analysis.entities[ $scope.newEntity.id ], scope

  $scope.$on "updateOccurencesForEntity", (event, entityId, occurrences) ->
    
    $log.debug "Occurrences #{occurrences.length} for #{entityId}"
    $scope.analysis.entities[ entityId ].occurrences = occurrences
    
    if occurrences.length is 0
      for box, entities of $scope.selectedEntities
        delete $scope.selectedEntities[ box ][ entityId ]
        
  # Observe current annotation changed
  $scope.$watch "annotation", (newAnnotationId)->
    
    $log.debug "Current annotation id changed to #{newAnnotationId}"
    # Execute just once the analysis is properly performed
    return if $scope.isRunning
    # Execute just if the current annotation id is defined
    return unless newAnnotationId?
    # Create new entity object
    $scope.newEntity = AnalysisService.createEntity()
    # Retrieve the current annotation
    annotation = $scope.analysis.annotations[ newAnnotationId ]
    # Set the entity label accordingly to the current annotation
    $scope.newEntity.label = annotation.text
    # Look for SameAs suggestions
    AnalysisService.getSuggestedSameAs annotation.text

  $scope.$on "currentUserLocalityDetected", (event, locality) ->
    $log.debug "Looking for entities matching with #{locality}"
    AnalysisService._innerPerform locality
    .then (response)->
      $scope.suggestedPlaces = {}
      for id, entity of response.data.entities
        if 'place' is entity.mainType 
          entity.id = id
          $scope.suggestedPlaces[ id ] = entity
      $scope.isGeolocationRunning = false    
  
  $scope.$on "geoLocationError", (event, error) ->
    $scope.isGeolocationRunning = false
    
  $scope.$on "textAnnotationClicked", (event, annotationId) ->
    $scope.annotation = annotationId
    # Close new entity creation forms if needed
    for id, box of $scope.boxes 
      box.addEntityFormIsVisible = false
    
  $scope.$on "textAnnotationAdded", (event, annotation) ->
    $log.debug "added a new annotation with Id #{annotation.id}"  
    # Add the new annotation to the current analysis
    $scope.analysis.annotations[ annotation.id ] = annotation
    # Set the annotation scope
    $scope.annotation = annotation.id
    
  $scope.$on "sameAsRetrieved", (event, sameAs) ->
    $scope.newEntity.suggestedSameAs = sameAs
  
  $scope.$on "relatedPostsLoaded", (event, posts) ->
    $scope.relatedPosts = posts
  
  $scope.$on "analysisPerformed", (event, analysis) -> 
    $scope.analysis = analysis

    # Topic Preselect
    if $scope.configuration.topic?
      for id, topic of analysis.topics
        if id in $scope.configuration.topic.sameAs
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
          $scope.images = $scope.images.concat entity.images

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

  $scope.onSelectedEntityTile = (entity, scope)->
    $log.debug "Entity tile selected for entity #{entity.id} within #{scope.id} scope"

    if not $scope.selectedEntities[ scope.id ][ entity.id ]?
      $scope.selectedEntities[ scope.id ][ entity.id ] = entity      
      # Concat entity images to suggested images collection
      $scope.images = $scope.images.concat entity.images
      # Notify entity selection
      $scope.$emit "entitySelected", entity, $scope.annotation
      # Reset current annotation
      $scope.selectAnnotation undefined
    else
      # Filter entity images to suggested images collection
      $scope.images = $scope.images.filter (img)-> 
        img not in entity.images  
      # Notify entity deselection
      $scope.$emit "entityDeselected", entity, $scope.annotation

    $scope.updateRelatedPosts()

  $scope.getLocation = ()->
    $scope.isGeolocationRunning = true
    GeoLocationService.getLocation()
  $scope.isPublishedPlace = (entity)->
    entity.id is $scope.publishedPlace?.id    
  $scope.hasPublishedPlace = ()->
    $scope.publishedPlace? or $scope.suggestedPlaces?
  
  $scope.onPublishedPlaceSelected = (entity)->
    if $scope.publishedPlace?.id is entity.id
      $scope.publishedPlace = undefined
      return
    $scope.publishedPlace = entity  

  $scope.isTopic = (topic)->
    topic.id is $scope.topic?.id 
  $scope.onTopicSelected = (topic)->
    if $scope.topic?.id is topic.id
      $scope.topic = undefined
      return
    $scope.topic = topic    
      
])