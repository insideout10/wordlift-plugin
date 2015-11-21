angular.module('wordlift.tinymce.plugin.services.TextAnnotationService', [])
.service('TextAnnotationService', [ 'Helpers', (h)->
    service = {}

    ###*
     * Create a text annotation using the specified parameters.
     * @param {object} An object containing the parameters to set.
     * @return {object} A text annotation instance.
     ###
    service.create = (params = {}) ->

      # Set the defalut values.
      defaults =
        id: 'urn:local-text-annotation-' + h.uniqueId 32
        text: ''
        start: 0
        end: 0
        confidence: 0.0
        entityAnnotations: {}
        _item: null

      # Return the Text Annotation structure by merging the defaults with the provided params.
      h.merge defaults, params

    ###*
     * Create a text annotation.
     * @param {object} The text annotation raw data.
     * @param {object} The context data holding prefix -> URL key-value pairs.
     * @return {object} A text annotation.
     ###
    service.build = (item, context) ->
      # console.log "[ item :: #{item} ][ context :: #{context} ]"
      service.create
        id: h.get '@id', item, context
        text: h.get("#{FISE_ONT}selected-text", item, context)[VALUE]
        start: h.get "#{FISE_ONT}start", item, context
        end: h.get "#{FISE_ONT}end", item, context
        confidence: h.get FISE_ONT_CONFIDENCE, item, context
        entityAnnotations: {}
        _item: item

    # Find a text annotation in the provided collection given its start and end parameters.
    service.find = (textAnnotations, start, end) ->
      return textAnnotation for textAnnotationId, textAnnotation of textAnnotations when textAnnotation.start is start and textAnnotation.end is end


    ###*
     * Find a text annotation in the provided collection which matches the start and end values.
     * @param {object} A collection of text annotations.
     * @param {object} Text annotation used for search or to create a new text annotation.
     * @return {object} The text annotation matching the parameters or a new text annotation with those parameters.
     ###
    service.findOrCreate = (textAnnotations, textAnnotation) ->
        # Return the text annotation if existing.
        ta = service.find textAnnotations, textAnnotation.start, textAnnotation.end
        return ta if ta?

        # Create a new text annotation.
        ta = service.create
          text: textAnnotation.label
          start: textAnnotation.start
          end: textAnnotation.end
          confidence: 1.0

        textAnnotations[ta.id] = ta
        ta


    # Return the service instance.
    service
])

