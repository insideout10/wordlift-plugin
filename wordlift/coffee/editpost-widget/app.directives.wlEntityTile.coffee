angular.module('wordlift.editpost.widget.directives.wlEntityTile', [])
.directive('wlEntityTile', [ 'configuration','$log', (configuration, $log)->
    require: '?^wlClassificationBox'
    restrict: 'E'
    scope:
      entity: '='
      isSelected: '='
      showConfidence: '='
      onSelect: '&'
      onMore: '&'
    templateUrl: ()->
      configuration['ajax_url'] + '?action=wl_templates&name=wordlift-directive-entity-tile'
    link: ($scope, $element, $attrs, $boxCtrl) ->
      
      $scope.configuration = configuration
      # Add tile to related container scope
      $boxCtrl?.addTile $scope

      $scope.isOpened = false

      $scope.isInternal = ()->
        if $scope.entity.id.startsWith configuration.datasetUri
          return true
        return false

      $scope.toggle = ()->
        if !$scope.isOpened
          $boxCtrl?.closeTiles()
        $scope.isOpened = !$scope.isOpened

  ])
