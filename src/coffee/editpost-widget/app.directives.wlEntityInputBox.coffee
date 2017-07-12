angular.module('wordlift.editpost.widget.directives.wlEntityInputBox', [])
# The wlEntityInputBoxes prints the inputs and textareas with entities data.
.directive('wlEntityInputBox', ['configuration', '$log', (configuration, $log)->
    restrict: 'E'
    scope:
      entity: '='
    templateUrl: ()->
      configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-entity-input-box.html?ver=3.12.1'
])