angular.module('wordlift.tinymce.plugin.services.EntityService', ['wordlift.tinymce.plugin.config'])
# The EntityService manipulates the entities selected, so that they can be sent using a POST to the back-end and
# persisted accordingly.
# The back-end expects an array of entities each containing the following:
# * *string* label
# * *string* type
# * *string* description
# * *array of strings* images
  .service('EntityService', ['$log', ($log) ->

    # Set a reference to the form container.
    container = $('#wordlift_selected_entitities_box')

    # Select the specified entity annotation.
    select : (entityAnnotation) ->
      $log.info 'select'
      $log.info entityAnnotation

      # Set a reference to...
      # ...the entity
      entity = entityAnnotation.entity
      # ...the ID
      id     = entity.id
      # ...the label
      label   = entity.label
      # ...the description
      description = if entity.description? then entity.description else ''
      # ...the images
      images  = entity.thumbnails
      # ...the type
      type    = entity.type

      # Create the entity div.
      entityDiv = $("<div itemid='#{id}'></div>")
        .append("<input type='text' name='wl_entities[#{id}][uri]' value='#{id}'>")
        .append("<input type='text' name='wl_entities[#{id}][label]' value='#{label}'>")
        .append("<input type='text' name='wl_entities[#{id}][description]' value='#{description}'>")
        .append("<input type='text' name='wl_entities[#{id}][type]' value='#{type}'>")

      # Append the images.
      if angular.isArray images
        entityDiv.append("<input type='text' name='wl_entities[#{id}][image]' value='#{image}'>") for image in images
      else
        entityDiv.append("<input type='text' name='wl_entities[#{id}][image]' value='#{images}'>")

      # Finally append the entity div to the container.
      container.append entityDiv

    # Deselect the specified entity annotation.
    deselect : (entityAnnotation) ->
      $log.info 'deselect'
      $log.info entityAnnotation

      # Set a reference to...
      # ...the entity
      entity = entityAnnotation.entity
      # ...the ID
      id     = entity.id

      # Remove the element for the provided entity.
      $("div[itemid='#{id}']").remove()
  ])
