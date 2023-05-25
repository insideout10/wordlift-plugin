angular.module('wordlift.editpost.widget.directives.wlEntityInputBox', [])
# The wlEntityInputBoxes prints the inputs and textareas with entities data.
.directive('wlEntityInputBox', ['configuration', '$log', (configuration, $log)->
    restrict: 'E'
    scope:
      entity: '='
    templateUrl: ()->
      configuration['ajax_url'] + '?action=wl_templates&name=wordlift-directive-entity-input-box'
])
