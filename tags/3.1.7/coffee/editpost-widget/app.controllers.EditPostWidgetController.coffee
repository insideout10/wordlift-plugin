angular.module('wordlift.editpost.widget.controllers.EditPostWidgetController', [
  'wordlift.editpost.widget.services.AnalysisService'
  'wordlift.editpost.widget.services.EditorService'
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
.controller('EditPostWidgetController', ['RelatedPostDataRetrieverService', 'EditorService', 'AnalysisService', 'configuration', '$log', '$scope', '$rootScope', '$injector', (RelatedPostDataRetrieverService, EditorService, AnalysisService, configuration, $log, $scope, $rootScope, $injector)-> 

  $scope.isRunning = false
  $scope.analysis = undefined
  $scope.relatedPosts = undefined
  $scope.newEntity = AnalysisService.createEntity()
  $scope.selectedEntities = {}
  $scope.annotation = undefined
  $scope.boxes = []
  $scope.images = {}
  $scope.isThereASelection = false
  $scope.configuration = configuration
  $scope.errors = []
  
  $rootScope.$on "analysisFailed", (event, errorMsg) ->
    $scope.errors.push errorMsg

  $rootScope.$on "analysisServiceStatusUpdated", (event, newStatus) ->
    $scope.isRunning = newStatus
    # When the analysis is running the editor is disabled and viceversa
    EditorService.updateContentEditableStatus !newStatus

  # Watch editor selection status
  $rootScope.$watch 'selectionStatus', ()->
    $scope.isThereASelection = $rootScope.selectionStatus

  for box in $scope.configuration.classificationBoxes
    $scope.selectedEntities[ box.id ] = {}
          
  # Delegate to EditorService
  $scope.createTextAnnotationFromCurrentSelection = ()->
    EditorService.createTextAnnotationFromCurrentSelection()
  # Delegate to EditorService
  $scope.selectAnnotation = (annotationId)->
    EditorService.selectAnnotation annotationId
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
    # Create new entity object
    $scope.newEntity = AnalysisService.createEntity()

  

  $scope.$on "updateOccurencesForEntity", (event, entityId, occurrences) ->
    
    $log.debug "Occurrences #{occurrences.length} for #{entityId}"
    $scope.analysis.entities[ entityId ].occurrences = occurrences
    
    if occurrences.length is 0
      for box, entities of $scope.selectedEntities
        delete $scope.selectedEntities[ box ][ entityId ]
        

  $scope.$on "textAnnotationClicked", (event, annotationId) ->
    $scope.annotation = annotationId

  $scope.$on "textAnnotationAdded", (event, annotation) ->
    $log.debug "added a new annotation with Id #{annotation.id}"
    
    # Add the new annotation to the current analysis
    $scope.analysis.annotations[ annotation.id ] = annotation
    # Set the annotation scope
    $scope.annotation = annotation.id
    # Set the annotation text as label for the new entity
    $scope.newEntity.label = annotation.text
    # Set the annotation id as id for the new entity
    # Ask for SameAs suggestions
    AnalysisService.getSuggestedSameAs annotation.text

  $scope.$on "sameAsRetrieved", (event, sameAs) ->
    $log.debug "Retrieved sameAs #{sameAs}"
    $scope.newEntity.suggestedSameAs = sameAs
  
  $scope.$on "relatedPostsLoaded", (event, posts) ->
    $scope.relatedPosts = posts
  
  $scope.$on "analysisPerformed", (event, analysis) -> 
    
    $scope.analysis = analysis

    # Preselect 
    for box in $scope.configuration.classificationBoxes
      for entityId in box.selectedEntities  
        if entity = analysis.entities[ entityId ]

          if entity.occurrences.length is 0
            $log.warn "Entity #{entityId} selected as #{box.label} without valid occurences!"
            continue

          $scope.selectedEntities[ box.id ][ entityId ] = analysis.entities[ entityId ]
          
          for uri in entity.images
            $scope.images[ uri ] = entity.label
        else
          $log.warn "Entity with id #{entityId} should be linked to #{box.id} but is missing"
    
    $scope.updateRelatedPosts()

  $scope.updateRelatedPosts = ()->
    $log.debug "Going to update related posts box ..."
    entityIds = []
    for box, entities of $scope.selectedEntities
      for id, entity of entities
        entityIds.push id
    RelatedPostDataRetrieverService.load entityIds

  $scope.onSelectedEntityTile = (entity, scope)->
    $log.debug "Entity tile selected for entity #{entity.id} within '#{scope.id}' scope"
    $log.debug entity
    $log.debug scope

    if not $scope.selectedEntities[ scope.id ][ entity.id ]?
      $scope.selectedEntities[ scope.id ][ entity.id ] = entity
      for uri in entity.images
        $scope.images[ uri ] = entity.label
      $scope.$emit "entitySelected", entity, $scope.annotation
      # Reset current annotation
      $scope.selectAnnotation undefined
    else
      for uri in entity.images
        delete $scope.images[ uri ]
      $scope.$emit "entityDeselected", entity, $scope.annotation

    $scope.updateRelatedPosts()
      
    
 
      
])