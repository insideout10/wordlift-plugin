angular.module('wordlift.tinymce.plugin.services.Helpers', [])
.service('Helpers', [ ->
    service = {}

    # Merges two objects by copying overrides param onto the options.
    service.merge = (options, overrides) ->
      @extend (@extend {}, options), overrides

    service.extend = (object, properties) ->
      for key, val of properties
        object[key] = val
      object

    # Creates a unique ID of the specified length (default 8).
    service.uniqueId = (length = 8) ->
      id = ''
      id += Math.random().toString(36).substr(2) while id.length < length
      id.substr 0, length

    ###*
     * Expand a string using the provided context.
     * @param {string} A content string to be expanded.
     * @param {object} A context providing prefix -> URL key-value pairs
     * @return {string} An expanded string.
     ###
    service._expand = (content, context) ->
      # console.log "_expand [ content :: #{content} ][ context :: #{context} ]"
      return if not content?
      # if there's no prefix, return the original string.
      if null is matches = "#{content}".match(/([\w|\d]+):(.*)/)
        prefix = content
        path = ''
      else
        # get the prefix and the path.
        prefix = matches[1]
        path = matches[2]

      # if the prefix is unknown, leave it.
      return content if not context[prefix]?

      prepend = if angular.isString context[prefix] then context[prefix] else context[prefix]['@id']

      #      console.log "_expand [ content :: #{content} ][ prepend :: #{prepend} ][ path :: #{path} ]"

      # return the full path.
      prepend + path

    ###*
     * Expand the specified content using the prefixes in the provided context.
     * @param {string|array} The content string or an array of strings.
     * @param {object} A context made of prefix -> URLs value pairs.
     * @return {string|array} An expanded string or an array of expanded strings.
     ###
    service.expand = (content, context) ->
      if angular.isArray content
        return (service.expand(c, context) for c in content)

      service._expand content, context

    # Get the values associated with the specified key(s). Keys are expanded.
    service.get = (what, container, context, filter) ->
      # If it's a single key, call getA
      return service.getA(what, container, context, filter) if not angular.isArray what

      # Prepare the return array.
      values = []

      # For each key, add the result.
      for key in what
        add = service.getA key, container, context, filter
        # Ensure the result is an array.
        add = if angular.isArray add then add else [ add ]
        # Merge unique the results.
        service.mergeUnique values, add

      # Return the result array.
      values

    # Get the values associated with the specified key. Keys are expanded.
    service.getA = (what, container, context, filter = ((a) -> a)) ->
      # expand the what key.
      whatExp = service.expand what, context
      # return the value bound to the specified key.
      #        console.log "[ what exp :: #{whatExp} ][ key :: #{expand key} ][ value :: #{value} ][ match :: #{whatExp is expand(key)} ]" for key, value of container
      return filter(value) for key, value of container when whatExp is service.expand(key, context)
      []

    # get the value for specified property (what) in the provided container in the specified language.
    # items must conform to {'@language':..., '@value':...} format.
    service.getLanguage = (what, container, language, context) ->
      # if there's no item return null.
      return if null is items = service.get(what, container, context)
      # transform to an array if it's not already.
      items = if angular.isArray items then items else [ items ]
      # cycle through the array.
      return item[VALUE] for item in items when language is item['@language']
      # if not found return the english value.
      return item[VALUE] for item in items when 'en' is item['@language']

    service.mergeUnique = (array1, array2) ->
      array1 = [] if not array1?
      array1.push item for item in array2 when item not in array1

    service.containsOrEquals = (what, where, context) ->
      return false if not where?
      # ensure the where argument is an array.
      whereArray = if angular.isArray where then where else [ where ]
      # expand the what string.
      whatExp = service.expand what, context
      # return true if the string is found.
      return true for item in whereArray when whatExp is service.expand(item, context)
      # otherwise false.
      false

    # Return the services.
    service

  ])