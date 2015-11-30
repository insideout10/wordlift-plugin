angular.module('wordlift.tinymce.plugin.services.EntityAnnotationConfidenceService', [])
.service('EntityAnnotationConfidenceService', [ 'EntityService','Helpers', '$log', (EntityService, h, $log) ->
  
  service = 
  	_entities: {}

  # Set the local entity collection.
  service.setEntities = (entities) ->
  	@_entities = entities

  service.getDefault = ()->
  	DEFAULT_ENTITY_ANNOTATION_CONFIDENCE_LEVEL

  # Enhanche entity annotation confidence following these criteria:
  # Add x if the related entity is described in more than 1 vocabulary
  # Add x if the related entity is described within Wordlift vocabulary
  # Add x if the related entity is described only within Wordlift vocabulary
  # Add x if the related entity is related to the current post 
  service.enhanceConfidenceFor = (entityAnnotation)->
    
    delta = 0
 	
    if entityAnnotation.entity.sources.length > 1
      delta += 0.20
    if WORDLIFT in entityAnnotation.entity.sources
      delta += 0.20
    if entityAnnotation.entity.source is WORDLIFT 
      delta += 1.0
    if EntityService.checkIfIsIncluded(@_entities, uri: entityAnnotation.entity.id) is true
      delta += 1.0

    $log.debug "Entity annotation #{entityAnnotation.id} enhancement: going to add #{delta} to confidence #{entityAnnotation.confidence}"
    entityAnnotation.confidence += delta
    $log.debug entityAnnotation
    entityAnnotation
    
  service
])