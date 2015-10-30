angular.module('wordlift.utils.directives', [])
.directive('wlSrc', ['$window', '$log', ($window, $log)->
  restrict: 'A'
  priority: 99 # it needs to run after the attributes are interpolated
  link: ($scope, $element, $attrs, $ctrl) ->  
    $element.bind('error', ()->
      unless $attrs.src is $attrs.wlSrc
        $log.warn "Error on #{$attrs.src}! Going to fallback on #{$attrs.wlSrc}"
        $attrs.$set 'src', $attrs.wlSrc
    )
])