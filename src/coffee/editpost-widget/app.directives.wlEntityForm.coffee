angular.module('wordlift.editpost.widget.directives.wlEntityForm', [])
.directive('wlEntityForm', ['configuration', '$window', '$log', (configuration, $window, $log)->
    restrict: 'E'
    scope:
      entity: '='
      onSubmit: '&'
      box: '='
    templateUrl: ()->
      configuration.defaultWordLiftPath + 'templates/wordlift-widget-be/wordlift-entity-panel.html'

    link: ($scope, $element, $attrs, $ctrl) ->  

      $scope.configuration = configuration

      $scope.getCurrentCategory = ()->
        return configuration.getCategoryForType $scope.entity.mainType
      
      $scope.removeCurrentImage = (index)->
        removed = $scope.entity.images.splice index, 1
        $log.warn "Removed #{removed} from entity #{$scope.entity.id} images collection"

      $scope.linkTo = (linkType)->
        $window.location.href = ajaxurl + '?action=wordlift_redirect&uri=' + $window.encodeURIComponent($scope.entity.id) + "&to=" + linkType

      $scope.hasOccurences = ()->
        $scope.entity.occurrences?.length > 0
      $scope.setSameAs = (uri)->
        $scope.entity.sameAs = uri

      $scope.isNew = (uri)->
        return !/^(f|ht)tps?:\/\//i.test $scope.entity.id 

])
