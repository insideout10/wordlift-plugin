# This directive is a small placeholder to have React load itself.
angular.module('wordlift.editpost.widget.directives.wlEntityList', [])
  .directive('wlEntityList', ['$log', ($log) ->

    restrict: 'A'

    # Trigger the event which will load the React application.
    link: () -> wp.wordlift.trigger 'wlEntityList.loaded'

])
