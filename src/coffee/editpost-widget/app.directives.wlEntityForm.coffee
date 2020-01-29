angular.module('wordlift.editpost.widget.directives.wlEntityForm', [])
.directive('wlEntityForm', ['configuration', '$window', '$log', (configuration, $window, $log)->
    restrict: 'E'
    scope:
      entity: '='
      onSubmit: '&'
      onReset: '&'
      box: '='
    templateUrl: ()->
      configuration['ajax_url'] + '?action=wl_templates&name=wordlift-directive-entity-form'

    link: ($scope, $element, $attrs, $ctrl) ->

      $scope.configuration = configuration
      $scope.currentCategory = undefined

      $scope.$watch 'entity.id', (entityId)->
        if entityId?
          $log.debug "Entity updated to #{entityId}"
          category = configuration.getCategoryForType $scope.entity?.mainType
          $log.debug "Going to update current category to #{category}"
          $scope.currentCategory = category

      $scope.onSubmitWrapper = (e)->
        e.preventDefault()
        $scope.onSubmit()

      $scope.onResetWrapper = (e)->
        e.preventDefault()
        $scope.onReset()

      $scope.setCurrentCategory = (categoryId)->
        $scope.currentCategory = categoryId
        # If there is only one type related the new category
        # Type selection is forced
        types = configuration.getTypesForCategoryId( categoryId )
        $log.debug "Going to check types"
        $log.debug types
        if types.length is 1
          $scope.setType( types[0] )


      $scope.unsetCurrentCategory = ()->
        $scope.currentCategory = undefined
        # Entity type has to be reset too
        $scope.entity?.mainType = undefined

      $scope.isSameAsOf = (sameAs)->
        sameAs.id in $scope.entity.sameAs

      $scope.addSameAs = (sameAs)->

        unless $scope.entity?.sameAs
          $scope.entity?.sameAs = []

        if sameAs.id in $scope.entity.sameAs
          index = $scope.entity.sameAs.indexOf sameAs.id
          $scope.entity.sameAs.splice index, 1
        else
          $scope.entity?.sameAs.push sameAs.id

      $scope.setType = (entityType)->
        return if entityType is $scope.entity?.mainType
        $scope.entity?.mainType = entityType

      $scope.isCurrentType = (entityType)->
        return $scope.entity?.mainType is entityType

      $scope.getAvailableTypes = ()->
        return configuration.getTypesForCategoryId $scope.currentCategory

      $scope.removeCurrentImage = (index)->
        removed = $scope.entity.images.splice index, 1
        $log.warn "Removed #{removed} from entity #{$scope.entity.id} images collection"

      $scope.linkToEdit = (e)->
        e.preventDefault()
        $window.location.href = ajaxurl + '?action=wordlift_redirect&uri=' + $window.encodeURIComponent($scope.entity.id) + "&to=edit"

      $scope.hasOccurences = ()->
        $scope.entity.occurrences?.length > 0

      $scope.setSameAs = (uri)->
        $scope.entity.sameAs = uri

      $scope.isInternal = ()->
        configuration.isInternal $scope.entity?.id

      $scope.isNew = (uri)->
        return !/^(f|ht)tps?:\/\//i.test $scope.entity?.id

])
