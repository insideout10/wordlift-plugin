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

        # Return `what` by default.
        return "what"

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


