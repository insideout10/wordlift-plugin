angular.module('wordlift.tinymce.plugin.services.EntityAnnotationService', [])
.service('EntityAnnotationService', [ 'EntityAnnotationConfidenceService', 'Helpers', 'LoggerService', (EntityAnnotationConfidenceService, h, LoggerService) ->
      
    service = {}

    # Create an entity annotation using the provided params.
    service.create = (params) ->

      defaults =
        id: 'uri:local-entity-annotation-' + h.uniqueId(32)
        label: ''
        confidence: EntityAnnotationConfidenceService.getDefault()
        entity: null
        relation: null
        selected: false
        _item: null

      # Copy over the label from the entity annotation if the label is not set on the entity.
      params.entity.label = params.label if params.entity? and not params.entity.label?

      # Merge the params with the default settings.
      entityAnnotation = h.merge defaults, params
      # Enhance confidence rate depending on related entity properties
      EntityAnnotationConfidenceService.enhanceConfidenceFor entityAnnotation

      entityAnnotation

    ###*
     * Create an entity annotation. An entity annotation is created for each related text-annotation.
     * @param {object} Entity raw data.
     * @param {string} The language code.
     * @return {array} An array of entity annotations.
     ###
    service.build = (item, language, entities, tas, context) ->
      # Get the reference to the entity.
      reference = h.get "#{FISE_ONT}entity-reference", item, context
      # If the referenced entity is not found, return null
      return [] if not entities[reference]?

      # Prepare the return array.
      annotations = []

      # get the related text annotation.
      relations = h.get "#{DCTERMS}relation", item, context
      # Ensure we're dealing with an array.
      relations = if angular.isArray relations then relations else [ relations ]

      # For each text annotation bound to this entity annotation, create an entity annotation and add it to the text annotation.
      for relation in relations
        textAnnotation = tas[relation]

        # Create an entity annotation.
        entityAnnotation = service.create
          id: h.get '@id', item, context
          label: h.getLanguage "#{FISE_ONT}entity-label", item, language, context
          confidence: h.get FISE_ONT_CONFIDENCE, item, context
          entity: entities[reference]
          relation: textAnnotation
          _item: item

        # Create a binding from the textannotation to the entity annotation.
        textAnnotation.entityAnnotations[entityAnnotation.id] = entityAnnotation if textAnnotation?

        # Accumulate the annotations.
        annotations.push entityAnnotation

      # Return the entity annotations.
      annotations

    # Find an entity annotation with the provided filters.
    service.find = (entityAnnotations, filter) ->
      if filter.uri?
        return (entityAnnotation for id, entityAnnotation of entityAnnotations when filter.uri is entityAnnotation.entity.id or filter.uri in entityAnnotation.entity.sameAs)

      if filter.selected?
        return (entityAnnotation for id, entityAnnotation of entityAnnotations when entityAnnotation.selected is filter.selected)


    # Return the service instance
    service
  ])