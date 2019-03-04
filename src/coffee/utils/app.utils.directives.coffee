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
.directive('wlHideAfter', ['$timeout', '$log', ($timeout, $log)->
  restrict: 'A'
  link: ($scope, $element, $attrs, $ctrl) ->
    delay = +$attrs.wlHideAfter
    $timeout(()->
      $log.debug "Remove msg after #{delay} ms"
      $element.hide()
    , delay)
])
.directive('wlClipboard', ['$timeout', '$document', '$log', ($timeout, $document, $log)->
  restrict: 'E'
  scope:
    text: '='
    onCopied: '&'
  transclude: true
  template: """
    <span
      class="wl-widget-post-link"
      ng-class="{'wl-widget-post-link-copied' : $copied}"
      ng-click="copyToClipboard()">
      <ng-transclude></ng-transclude>
      <input type="text" ng-value="text" />
    </span>
  """
  link: ($scope, $element, $attrs, $ctrl) ->

    $scope.$copied = false

    $scope.node = $element.find 'input'
    $scope.node.css 'position', 'absolute'
    $scope.node.css 'left', '-10000px'

    # $element
    $scope.copyToClipboard = ()->
      try

        # Set inline style to override css styles
        $document[0].body.style.webkitUserSelect = 'initial'
        selection = $document[0].getSelection()
        selection.removeAllRanges()
        # Fake node selection
        $scope.node.select()
        # Perform the task
        unless $document[0].execCommand 'copy'
           $log.warn "Error on clipboard copy for #{text}"
        selection.removeAllRanges()
        # Update copied status and reset after 3 seconds
        $scope.$copied = true
        $timeout(()->
          $log.debug "Going to reset $copied status"
          $scope.$copied = false
        , 3000)

        # Execute onCopied callback
        if angular.isFunction($scope.onCopied)
          $scope.$evalAsync $scope.onCopied()

      finally
        $document[0].body.style.webkitUserSelect = ''
])