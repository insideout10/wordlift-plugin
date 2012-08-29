(function() {
  var $, app;

  $ = jQuery;

  app = angular.module("wordlift.disambiguation", []);

  app.controller("AnnotationCtrl", function($scope, $http) {
    var images, postID;
    postID = $("#post_ID").val();
    $http({
      "method": "GET",
      "url": "admin-ajax.php",
      "params": {
        "action": "wordlift.dump",
        "postID": postID
      }
    }).success(function(data) {
      $scope.annotations = data.annotations;
      return $scope.entities = data.entities;
    });
    images = {
      "http://schema.org/Place": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_462.svg",
      "http://schema.org/Organization": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_921.svg",
      "http://schema.org/CreativeWork": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_2572.svg",
      "http://schema.org/Person": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_419%202.svg"
    };
    $scope.getImageURL = function(type) {
      return images[type];
    };
    return $scope.bindEntityToPost = function(annotation, entity) {
      var method;
      annotation.binding = true;
      method = entity.selected ? "DELETE" : "POST";
      return $http({
        "method": method,
        "url": "admin-ajax.php",
        "params": {
          "action": "wordlift.post/entities",
          "postID": 8633,
          "entity": entity.entityReference,
          "textAnnotation": annotation.subject
        }
      }).success(function(data, status, headers, config) {
        var e, _i, _len, _ref;
        _ref = $scope.entities;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          e = _ref[_i];
          if (e.relation === annotation.subject) {
            e.selected = false;
          }
        }
        if (method === "POST") {
          entity.selected = true;
        }
        return annotation.binding = false;
      }).error(function(data, status, headers, config) {
        return annotation.binding = false;
      });
    };
  });

  angular.bootstrap($("#wordlift-disambiguation"), ["wordlift.disambiguation"]);

}).call(this);
