angular.module('wordlift.editpost.widget.directives.wlEntityInputBox', [])
# The wlEntityInputBoxes prints the inputs and textareas with entities data.
.directive('wlEntityInputBox', ->
    restrict: 'E'
    scope:
      entity: '='
    template: """
        <div>

          <input type='text' name='wl_entities[{{entity.id}}][uri]' value='{{entity.id}}'>
          <input type='text' name='wl_entities[{{entity.id}}][label]' value='{{entity.label}}'>
          <textarea name='wl_entities[{{entity.id}}][description]'>{{entity.description}}</textarea>
          <input type='text' name='wl_entities[{{entity.id}}][main_type]' value='wl-{{entity.mainType}}'>

          <input ng-repeat="type in entity.types" type='text'
          	name='wl_entities[{{entity.id}}][type][]' value='{{type}}' />
          <input ng-repeat="image in entity.images" type='text'
            name='wl_entities[{{entity.id}}][image][]' value='{{image}}' />
          <input ng-repeat="sameAs in entity.sameAs" type='text'
            name='wl_entities[{{entity.id}}][sameas][]' value='{{sameAs}}' />

      	</div>
    """
  )