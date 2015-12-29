angular.module('wordlift.editpost.widget.directives.wlEntityTile', [])
.directive('wlEntityTile', [ 'configuration','$log', (configuration, $log)->
    require: '^wlClassificationBox'
    restrict: 'E'
    scope:
      entity: '='
      isSelected: '='
      onEntitySelect: '&'
    template: """
  	  <div ng-class="'wl-' + entity.mainType" class="entity">
        <div class="entity-header">
  	      
          <i ng-click="onEntitySelect()" ng-hide="annotation" ng-class="{ 'wl-selected' : isSelected, 'wl-unselected' : !isSelected }"></i>
          <i ng-click="onEntitySelect()" class="type"></i>
          <span class="label" ng-click="onEntitySelect()">{{entity.label}}</span>

          <small ng-show="entity.occurrences.length > 0">({{entity.occurrences.length}})</small>
          <span ng-show="isInternal()">*</span>  
          <i ng-class="{ 'wl-more': isOpened == false, 'wl-less': isOpened == true }" ng-click="toggle()"></i>
  	    </div>
        <div class="details" ng-show="isOpened">
          <wl-entity-form entity="entity" on-submit="toggle()"></wl-entity-form>
        </div>
  	  </div>
  	"""
    link: ($scope, $element, $attrs, $boxCtrl) ->				      
      
      # Add tile to related container scope
      $boxCtrl.addTile $scope

      $scope.isOpened = false
      
      $scope.isInternal = ()->
        if $scope.entity.id.startsWith configuration.datasetUri
          return true
        return false 
      
      $scope.open = ()->
      	$scope.isOpened = true
      $scope.close = ()->
      	$scope.isOpened = false  	
      $scope.toggle = ()->
        if !$scope.isOpened 
          $boxCtrl.closeTiles()    
        $scope.isOpened = !$scope.isOpened
        
  ])
