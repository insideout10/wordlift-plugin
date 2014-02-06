angular.module( 'AnalysisService', [] )
  .service( 'AnalysisService', [ '$log', ($log) ->

    parse: (data) ->

      languages         = []
      textAnnotations   = {}
      entityAnnotations = {}
      entities          = {}

      # support functions:

      createEntity = (item, language) ->
        {
          id          : get('@id', item),
          types       : get('@type', item),
          thumbnails  : get('foaf:depiction', item),
          description : getLanguage('rdfs:comment', item, language),
          descriptions: get('rdfs:comment', item),
          label       : getLanguage('rdfs:label', item, language),
          labels      : get('rdfs:label', item),
          _item       : item
        }

      createEntityAnnotation = (item) ->
        {
          id        : get('@id', item),
          label     : get('enhancer:entity-label', item),
          confidence: get('enhancer:confidence', item),
          _item     : item,
          entity    : entities[get('enhancer:entity-reference', item)]
        }

      createTextAnnotation = (item) ->
        {
          id             : get('@id', item),
          selectedText   : get('enhancer:selected-text', item)['@value'],
          selectionPrefix: get('enhancer:selection-prefix', item)['@value'],
          selectionSuffix: get('enhancer:selection-suffix', item)['@value'],
          confidence     : get('enhancer:confidence', item),
          _item          : item
        }

      createLanguage = (item) ->
        {
          code      : get('dc:language', item),
          confidence: get('enhancer:confidence', item)
          _item     : item
        }

      # get the provided key from the container, expanding the keys when necessary.
      get = (what, container) ->
        # expand the what key.
        whatExp = expand(what)
        # return the value bound to the specified key.
        return value for key, value of container when whatExp is expand(key)
        null

      # get the value for specified property (what) in the provided container in the specified language.
      # items must conform to {'@language':..., '@value':...} format.
      getLanguage =  (what, container, language) ->
        # if there's no item return null.
        return if null is items = get(what, container)
        # transform to an array if it's not already.
        items = if angular.isArray items then items else [ items ]
        # cycle through the array.
        return item['@value'] for item in items when language is item['@language']
        # if not found return null.
        null

      containsOrEquals = (what, where) ->
        # if where is not defined return false.
        return false if not where?
        # ensure the where argument is an array.
        whereArray = if angular.isArray where then where else [ where ]
        # expand the what string.
        whatExp    = expand(what)
        # return true if the string is found.
        return true for item in whereArray when whatExp is expand(item)
        # otherwise false.
        false

      # expand a string to a full path if it contains a prefix.
      expand = (content) ->
        # if there's no prefix, return the original string.
        if null is matches = content.match(/([\w|\d]+):(.*)/)
          return content

        # get the prefix and the path.
        prefix  = matches[1]
        path    = matches[2]

        # if the prefix is unknown, leave it.
        prepend = if prefixes[prefix]? then prefixes[prefix] else "#{prefix}:"

        # return the full path.
        prepend + path

      # data is split in a context and a graph.
      context  = data['@context']
      graph    = data['@graph']

      # get the prefixes.
      prefixes = []

      # cycle in the context definitions and extract the prefixes.
      for key, value of context
        # consider a prefix only keys w/o ':' and the value is string.
        if -1 is key.indexOf(':') and angular.isString(value)
          # add the prefix.
          prefixes[key] = value

      for item in graph
        id     = item['@id']
        types  = item['@type']
        dctype = get('dc:type', item)

        # TextAnnotation/LinguisticSystem
        if containsOrEquals('enhancer:TextAnnotation', types) and containsOrEquals('dc:LinguisticSystem', dctype)
          languages.push createLanguage(item)

        # TextAnnotation
        else if containsOrEquals('enhancer:TextAnnotation', types)
          $log.debug "TextAnnotation [ @id :: #{id} ][ types :: #{types} ]"
          textAnnotations[id] = item

        # EntityAnnotation
        else if containsOrEquals('enhancer:EntityAnnotation', types)
          $log.debug "EntityAnnotation [ @id :: #{id} ][ types :: #{types} ]"
          entityAnnotations[id] = item

        # Entity
        else
          $log.debug "Entity [ @id :: #{id} ][ types :: #{types} ]"
          entities[id] = item

      # sort the languages by confidence.
      languages.sort (a, b) ->
        if a.confidence < b.confidence
          return -1
        if a.confidence > b.confidence
          return 1
        0

      # create a reference to the default language.
      language = languages[0].code

      # create entities instances in the entities array.
      entities[id] = createEntity(item, language) for id, item of entities

      # create entities instances in the entities array.
      entityAnnotations[id] = createEntityAnnotation(item) for id, item of entityAnnotations

      # create entities instances in the entities array.
      textAnnotations[id] = createTextAnnotation(item) for id, item of textAnnotations

      analysis = {
        language         : language,
        entities         : entities,
        entityAnnotations: entityAnnotations,
        textAnnotations  : textAnnotations,
        languages        : languages
      }

      $log.debug analysis
  ])
