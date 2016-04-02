angular.module('wordlift.utils.directives', [])
# See https://github.com/angular/angular.js/blob/master/src/ng/directive/ngEventDirs.js
.directive('wlOnError', ['$parse', '$window', '$log', ($parse, $window, $log)->
  restrict: 'A'
  compile: ($element, $attrs) ->  
    return (scope, element)->
      fn = $parse($attrs.wlOnError)
      element.on('error', (event)->
        callback = ()->
      	  fn(scope, { $event: event })
        scope.$apply(callback)
      )
])
.directive('wlFallback', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  priority: 99 # it needs to run after the attributes are interpolated
  link: ($scope, $element, $attrs, $ctrl) ->  
    $element.bind('error', ()->
      unless $attrs.src is $attrs.wlFallback
        $log.warn "Error on #{$attrs.src}! Going to fallback on #{$attrs.wlFallback}"
        $attrs.$set 'src', $attrs.wlFallback
    )
])
.directive('wlClipboard', ['$document', '$log', ($document, $log)->
  restrict: 'E'
  scope:
    text: '='
    onSuccess: '&'
    onError: '&'
  transclude: true
  template: """
    <span class="wl-widget-post-link" ng-click="copyToClipboard()">
      <ng-transclude></ng-transclude>
      <input type="text" ng-value="text" />
    </span>
  """
  link: ($scope, $element, $attrs, $ctrl) ->  
    
    $scope.node = $element.find 'input'
    $scope.node.css 'position', 'absolute'
    $scope.node.css 'left', '-10000px'
    
    # $element
    $scope.copyToClipboard = ()->
      try
        # Set inline style to override css styles
        $log.debug "Going to copy #{$scope.text}"
        $document[0].body.style.webkitUserSelect = 'initial'
        selection = $document[0].getSelection()
        selection.removeAllRanges()
        # Fake node selection
        $scope.node.select()
        unless $document[0].execCommand 'copy'
           $log.debug "Going to copy #{$scope.text}"

        selection.removeAllRanges()
      finally
        $document[0].body.style.webkitUserSelect = ''
])