angular.module('wordlift.editpost.widget.directives.wlEntityForm', [])
.directive('wlEntityForm', ['configuration', '$window', '$log', (configuration, $window, $log)->
    restrict: 'E'
    scope:
      entity: '='
      onSubmit: '&'
      box: '='
    templateUrl: ()->
      configuration.defaultAngularTemplatesPath + 'wordlift-directive-entity-form.html'

    link: ($scope, $element, $attrs, $ctrl) ->  

      $scope.removeCurrentImage = ()->
        removed = $scope.entity.images.shift()
        $log.warn "Removed #{removed} from entity #{$scope.entity.id} images collection"
        
      $scope.getCurrentTypeUri = ()->
        for type in configuration.types
          if type.css is "wl-#{$scope.entity.mainType}"
            return type.uri

      $scope.isInternal = ()->
        if $scope.entity.id.startsWith configuration.datasetUri
          return true
        return false 
      
      $scope.linkTo = (linkType)->
        $window.location.href = ajaxurl + '?action=wordlift_redirect&uri=' + $window.encodeURIComponent($scope.entity.id) + "&to=" + linkType
      
      $scope.hasOccurences = ()->
        $scope.entity.occurrences?.length > 0
      $scope.setSameAs = (uri)->
        $scope.entity.sameAs = uri
      
      $scope.checkEntityId = (uri)->
        /^(f|ht)tps?:\/\//i.test(uri)

      availableTypes = [] 
      for type in configuration.types
        availableTypes[ type.css.replace('wl-','') ] = type.uri

      $scope.supportedTypes = ({ id: type.css.replace('wl-',''), name: type.uri } for type in configuration.types)
      if $scope.box
        $scope.supportedTypes = ({ id: type, name: availableTypes[ type ] } for type in $scope.box.registeredTypes)
        

])
