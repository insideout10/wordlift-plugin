angular.module('wordlift.editpost.widget.directives.wlClassificationBox', [])
.directive('wlClassificationBox', ['$log', ($log)->
    restrict: 'E'
    scope: true
    transclude: true      
    template: """
    	<div class="classification-box">
    		<div class="box-header">
          <h5 class="label">
            {{box.label}}
            <span ng-hide="addEntityFormIsVisible" ng-click="openAddEntityForm()" class="button" ng-class="{ 'button-primary selected wl-button' : hasAnalysis(), 'preview' : !hasAnalysis() }">Add entity</span>
          </h5>
          <wl-entity-form ng-show="addEntityFormIsVisible" entity="newEntity" box="box" on-submit="closeAddEntityForm()"></wl-entity-form>
          <div class="wl-selected-items-wrapper">
            <span ng-class="'wl-' + entity.mainType" ng-repeat="(id, entity) in selectedEntities[box.id]" class="wl-selected-item">
              {{ entity.label}}
              <i class="wl-deselect" ng-click="onSelectedEntityTile(entity, box)"></i>
            </span>
          </div>
        </div>
  			<div class="box-tiles">
          <div ng-transclude></div>
  		  </div>
      </div>	
    """
    link: ($scope, $element, $attrs, $ctrl) ->  	  
  	  
      $scope.addEntityFormIsVisible = false

      $scope.openAddEntityForm = ()->
        
        if !$scope.isThereASelection and !$scope.annotation?
          $scope.addError "Select a text or an existing annotation in order to create a new entity. Text selections are valid only if they do not overlap other existing annotations."
          return
        
        $scope.addEntityFormIsVisible = true
        
        if $scope.annotation?
          $log.debug "There is a current annotation already. Nothing to do"
          return

        $scope.createTextAnnotationFromCurrentSelection()
      
      $scope.closeAddEntityForm = ()->
        $scope.addEntityFormIsVisible = false
        $scope.addNewEntityToAnalysis $scope.box

      $scope.hasSelectedEntities = ()->
        Object.keys( $scope.selectedEntities[ $scope.box.id ] ).length > 0

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