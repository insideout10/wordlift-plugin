angular.module('wordlift.directives.wlEntityProps', [])
.directive('wlEntityProps', ->
    restrict: 'E'
    scope:
      textAnnotations: '='
    template: """
      <div class="wl-entity-props" ng-repeat="textAnnotation in textAnnotations">
        <div ng-repeat="ea in textAnnotation.entityAnnotations | filterObjectBy:'selected':true">
          <div ng-repeat="(k, ps) in ea.entity.props">
            <input ng-repeat="p in ps" name="wl_props[{{ea.entity.id}}][{{k}}][]" ng-value="p" type="text" />
          </div>
        </div>
      </div>
    """
  )