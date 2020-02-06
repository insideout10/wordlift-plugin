angular.module('wordlift.editpost.widget.directives.wlClassificationBox', [])
.directive('wlClassificationBox', ['configuration', '$log', (configuration, $log)->
    restrict: 'E'
    scope: true
    transclude: true
    templateUrl: ()->
      configuration['ajax_url'] + '?action=wl_templates&name=wordlift-directive-classification-box'
    link: ($scope, $element, $attrs, $ctrl) ->
      $log.debug 'Linking classification box...'

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
