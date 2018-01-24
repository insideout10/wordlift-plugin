angular.module('wordlift.editpost.widget.directives.wlClassificationBox', [])
.directive('wlClassificationBox', ['configuration', '$log', (configuration, $log)->
    restrict: 'E'
    scope: true
    transclude: true
    templateUrl: ()->
      configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-directive-classification-box.html'
    link: ($scope, $element, $attrs, $ctrl) ->

      $scope.hasSelectedEntities = ()->
        Object.keys( $scope.selectedEntities[ $scope.box.id ] ).length > 0

      wp.wordlift.trigger 'wlClassificationBox.loaded', $scope

    controller: ($scope, $element, $attrs) ->

      # Mantain a reference to nested entity tiles $scope
      # TODO manage on scope distruction event
      $scope.tiles = []

      $scope.boxes[ $scope.box.id ] = $scope

      ctrl = @
      ctrl.addTile = (tile)->
        $scope.tiles.push tile
      ctrl.closeTiles = ()->
        for tile in $scope.tiles
          tile.isOpened = false

])
