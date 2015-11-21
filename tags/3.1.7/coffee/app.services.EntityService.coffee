angular.module('wordlift.tinymce.plugin.services.EntityService', ['wordlift.tinymce.plugin.services.Helpers', 'LoggerService'])
.service('EntityService', [ 'Helpers', 'LoggerService', '$filter', (h, logger, $filter) ->
    service = {}

    # Find an entity in the provided entities collection using the provided filters.
    service.find = (entities, filter) ->
      if filter.uri?
        return (entity for entityId, entity of entities when filter.uri is entity?.id or filter.uri in entity?.sameAs)

    # Find first entity in the provided entities collection using the provided filters.
    service.checkIfIsIncluded = (entities, filter) ->
      entities = @find(entities, filter)
      if entities.length > 0 then return true else false

    ###*
     * Create an entity using the provided data and context.
     * @param {object} An item object containing the entity raw data.
     * @param {object} A context instance with prefix -> URL key-value pairs.
     * @return {object} An entity instance.
     ###
    service.create = (item, language, kt, context) ->
      # console.log "[ item :: #{item} ][ language :: #{language} ][ kt :: #{kt} ][ context :: #{context} ]"
      id = h.get '@id', item, context
      # Get the types expanding the type URI.
      types = h.get '@type', item, context, (ts) ->
        ts = if angular.isArray ts then ts else [ ts ]
        (h.expand(t, context) for t in ts)

      sameAs = h.get 'http://www.w3.org/2002/07/owl#sameAs', item, context
      sameAs = if angular.isArray sameAs then sameAs else [ sameAs ]

      fn = (values) ->
        values = if angular.isArray values then values else [ values ]
        for value in values
          match = /m\.(.*)$/i.exec value
          if null is match
            value
          else
            # If it's a Freebase URL normalize the link to the image.
            "https://usercontent.googleapis.com/#{FREEBASE}/v1/image/m/#{match[1]}?maxwidth=4096&maxheight=4096"

      # Get all the thumbnails; for each thumbnail execute the provided function.
      thumbnails = h.get ['http://xmlns.com/foaf/0.1/depiction',  "#{FREEBASE_NS}common.topic.image", "#{SCHEMA_ORG}image"], item, context, fn

      # Get the known types.
      #            $log.info "AnalysisService.parse [ known types :: "
      #            $log.info service
      #            $log.info service._knownTypes
      #            $log.info " ]"
      knownTypes = service.getKnownTypes types, kt, context
      # Get the stylesheet classes.
      css = knownTypes[0].type.css

      # create the entity model.
      entity =
        id: id
        thumbnail: if 0 < thumbnails.length then thumbnails[0] else null
        thumbnails: thumbnails
        css: css
        type: knownTypes[0].type.uri # This is the main type for the entity.
        types: types
        label: h.getLanguage RDFS_LABEL, item, language, context
        labels: h.get RDFS_LABEL, item, context
        sameAs: sameAs
        source: if id.match("^#{FREEBASE_COM}.*$")
          FREEBASE
        else if id.match("^#{DBPEDIA_ORG_REGEX}.*$")
          DBPEDIA
        else
          'wordlift'
        _item: item
        props: service.createProps item, context

      # Add sources as an array.
      entity.sources = [ entity.source ]

      entity.description = h.getLanguage [ RDFS_COMMENT, FREEBASE_NS_DESCRIPTION, SCHEMA_ORG_DESCRIPTION ], item, language, context
      entity.descriptions = h.get [ RDFS_COMMENT, FREEBASE_NS_DESCRIPTION, SCHEMA_ORG_DESCRIPTION ], item, context

      # Avoid null in entity description.
      entity.description = '' if not entity.description?

      entity.latitude = h.get "#{WGS84_POS}lat", item, context
      entity.longitude = h.get "#{WGS84_POS}long", item, context
      if 0 is entity.latitude.length or 0 is entity.longitude.length
        entity.latitude = ''
        entity.longitude = ''

      # Check if thumbnails exists.
      #        if thumbnails? and angular.isArray thumbnails
      #          $q.all(($http.head thumbnail for thumbnail in thumbnails))
      #            .then (results) ->
      #              # Populate the thumbnails array only with existing images (those that return *status code* 200).
      #              entity.thumbnails = (result.config.url for result in results when 200 is result.status)
      #              # Set the main thumbnail as the first.
      #              # TODO: use the lightest image as first.
      #              entity.thumbnail  = entity.thumbnails[0] if 0 < entity.thumbnails.length'

      # return the entity.
      #        console.log "createEntity [ entity id :: #{entity.id} ][ language :: #{language} ][ types :: #{types} ][ sameAs :: #{sameAs} ]"
      entity

    ###*
     * Merge the specified entity with the provided entities.
     *
     * @param {object} The entity to merge.
     * @param {object} A collection of entities to use for merging.
     *
     * @return {object} The merged entity.
     ###
    service.merge = (entity, entities) ->

      for sameAs in entity.sameAs
        if entities[sameAs]? and entities[sameAs] isnt entity

          existing = entities[sameAs]

          logger.debug "EntityService.merge : found a match [ entity 1 :: #{entity.id} ][ entity 2 :: #{existing.id} ]"

          h.mergeUnique entity.sameAs, existing.sameAs
          h.mergeUnique entity.thumbnails, existing.thumbnails
          h.mergeUnique entity.sources, existing.sources
          h.mergeUnique entity.types, existing.types

          entity.css = existing.css if not entity.css?
          entity.source = entity.sources.join(', ')
          # Prefer the DBpedia description.
          # TODO: have a user-set priority.
          entity.description = existing.description if DBPEDIA is existing.source
          entity.longitude = existing.longitude if DBPEDIA is existing.source and existing.longitude?
          entity.latitude = existing.latitude if DBPEDIA is existing.source and existing.latitude?

          # Delete the sameAs entity from the index.
          entities[sameAs] = entity
          service.merge entity, entities

      logger.debug "EntityService.merge [ id :: #{entity.id} ]", entity: entity

      entity


    ###*
     * Get the known type given the specified types.
     * @param {array} An array of types.
     * @param {object} An object representing the known types.
     * @return {object} The default type.
     ###
    service.getKnownTypes = (types, knownTypes, context) ->

      # An array with known types according to the specified types.
      returnTypes = []
      defaultType = undefined
      for kt in knownTypes
        # Set the default type, identified by an asterisk (*) in the sameAs values.
        defaultType = [
          { type: kt }
        ] if '*' in kt.sameAs
        # Get all the URIs associated to this known type.
        uris = kt.sameAs.concat kt.uri
        # If there is 1+ uri in common between the known types and the provided types, then add the known type.
        matches = (uri for uri in uris when h.containsOrEquals(uri, types, context))
        returnTypes.push { matches: matches, type: kt } if 0 < matches.length


      # Return the defaul type if not known types have been found.
      return defaultType if 0 is returnTypes.length

      # Sort and return the match types.
      $filter('orderBy') returnTypes, 'matches', true
      returnTypes

    ###*
     * Create a key-values pair of properties.
     * @param {object} An item object containing the entity raw data.
     * @param {object} A context instance with prefix -> URL key-value pairs.
     * @return {object} A key-values pair of entity properties.
     ###
    service.createProps = (item, context) ->
      # Initialize the props
      # console.log "createProps [ item :: #{item} ][ context :: #{context} ]"
      props = {}
      # Populate the props.
      for key, value of item
        # Ignore properties with object (most likely strings with the language code.
        # TODO: enable multilanguge WordLift here.
        continue if angular.isObject value
        expKey = h.expand key, context
        # console.log "createProps [ key :: #{key} ][ expKey :: #{expKey} ][ value :: #{value} ]"
        # Initialize the array.
        props[expKey] = [] if not props[expKey]?
        # Add the value to the array.
        props[expKey].push h.expand(value, context)

      # Return the props.
      props

    # Return the service instance.
    service
  ])
