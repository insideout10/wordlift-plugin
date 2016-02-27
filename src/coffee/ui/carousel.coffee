angular.module('wordlift.ui.carousel', [])
.directive('wlCarousel', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  scope: true
  transclude: true      
  template: """
      <div class="wl-carousel" ng-class="{ 'active' : areControlsVisible }" ng-show="panes.length > 0" ng-mouseover="showControls()" ng-mouseleave="hideControls()">
        <div class="wl-panes" ng-style="{ width: panesWidth, left: position }" ng-transclude></div>
        <div class="wl-carousel-arrows" ng-show="areControlsVisible">
          <i class="wl-angle-left" ng-click="prev()" ng-show="isPrevArrowVisible()" />
          <i class="wl-angle-right" ng-click="next()" ng-show="isNextArrowVisible()" />
        </div>
      </div>
  """
  controller: [ '$scope', '$element', '$attrs', '$log', ($scope, $element, $attrs, $log) ->
      
    w = angular.element $window

    $scope.setItemWidth = ()->
      $element.width() / $scope.visibleElements() 

    $scope.showControls = ()->
      $scope.areControlsVisible = true

    $scope.hideControls = ()->
      $scope.areControlsVisible = false

    $scope.visibleElements = ()->
      if $element.width() > 460
        return 4
      return 1

    $scope.isPrevArrowVisible = ()->
      ($scope.currentPaneIndex > 0)
    
    $scope.isNextArrowVisible = ()->
      ($scope.panes.length - $scope.currentPaneIndex) > $scope.visibleElements()
    
    $scope.next = ()->
      $scope.position = $scope.position - $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex + 1
    $scope.prev = ()->
      $scope.position = $scope.position + $scope.itemWidth
      $scope.currentPaneIndex = $scope.currentPaneIndex - 1
    
    $scope.setPanesWrapperWidth = ()->
      $scope.panesWidth = ( $scope.panes.length * $scope.itemWidth ) 
      $scope.position = 0;
      $scope.currentPaneIndex = 0

    $scope.itemWidth =  $scope.setItemWidth()
    $scope.panesWidth = undefined
    $scope.panes = []
    $scope.position = 0;
    $scope.currentPaneIndex = 0
    $scope.areControlsVisible = false

    w.bind 'resize', ()->
        
      $scope.itemWidth = $scope.setItemWidth();
      $scope.setPanesWrapperWidth()
      for pane in $scope.panes
        pane.scope.setWidth $scope.itemWidth
      $scope.$apply()

    ctrl = @
    ctrl.registerPane = (scope, element)->
      # Set the proper width for the element
      scope.setWidth $scope.itemWidth
        
      pane =
        'scope': scope
        'element': element

      $scope.panes.push pane
      $scope.setPanesWrapperWidth()

    ctrl.unregisterPane = (scope)->
        
      unregisterPaneIndex = undefined
      for pane, index in $scope.panes
        if pane.scope.$id is scope.$id
          unregisterPaneIndex = index

      $scope.panes.splice unregisterPaneIndex, 1
      $scope.setPanesWrapperWidth()
  ]   
])
.directive('wlCarouselPane', ['$log', ($log)->
  require: '^wlCarousel'
  restrict: 'EA'
  transclude: true 
  template: """
      <div ng-transclude></div>
  """
  link: ($scope, $element, $attrs, $ctrl) ->

    $log.debug "Going to add carousel pane with id #{$scope.$id} to carousel"
    $element.addClass "wl-carousel-item"
      
    $scope.setWidth = (size)->
      $element.css('width', "#{size}px")

    $scope.$on '$destroy', ()->
      $log.debug "Destroy #{$scope.$id}"
      $ctrl.unregisterPane $scope

    $ctrl.registerPane $scope, $element
])