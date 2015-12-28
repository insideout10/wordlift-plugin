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
        $log.warn "Error on #{$attrs.src}! Going to fallback on #{$attrs.wlSrc}"
        $attrs.$set 'src', $attrs.wlFallback
    )
])