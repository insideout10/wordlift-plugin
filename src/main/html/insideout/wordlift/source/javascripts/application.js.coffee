app = angular.module "wordlift.disambiguation", []

app.controller "AnnotationCtrl", ($scope, $http) ->

  $http.get( "http://localizeme.dyndns.org/wordlift/wp-admin/admin-ajax.php?action=wordlift.dump&postID=8633" )
    .success (data) ->
      $scope.annotations= data.annotations
      $scope.entities = data.entities

  images =
    "http://schema.org/Place": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_462.svg",
    "http://schema.org/Organization": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_921.svg",
    "http://schema.org/CreativeWork": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_2572.svg",
    "http://schema.org/Person": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_419%202.svg"


  $scope.getImageURL = (type) ->
    images[ type ]

  $scope.toggleMenu = () ->
    $scope.menuopen = !$scope.menuopen
#    annotation.menuopen = true if not annotation.menuopen?
#    annotation.menuopen = !annotation.menuopen
#    console.log( annotation.menuopen )




