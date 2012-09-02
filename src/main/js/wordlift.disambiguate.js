(function() {
  var $;

  $ = jQuery;

  angular.module("wordlift.disambiguate", []).controller("DisambiguationsController", function($scope, $http) {
    var postID;
    postID = $("#post_ID").val();
    $scope.disambiguations = [];
    $scope.clearAndBind = function(disambiguation, entity) {
      var method;
      disambiguation.working = true;
      method = entity.selected ? "DELETE" : "POST";
      return $http({
        "method": method,
        "url": "admin-ajax.php",
        "params": {
          "action": "wordlift.disambiguations",
          "entity": entity.about
        },
        "data": {
          "clear": disambiguation.textAnnotations,
          "bind": entity.textAnnotations
        }
      }).success(function(data) {
        var disambiguationEntity, _i, _len, _ref;
        _ref = disambiguation.entities;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          disambiguationEntity = _ref[_i];
          disambiguationEntity.selected = false;
        }
        if ("POST" === method) {
          entity.selected = true;
        }
        return disambiguation.working = false;
      }).error(function() {
        return disambiguation.working = false;
      });
    };
    $scope.getTypeName = function(typeURI) {
      return typeURI.match(/.+\/(.+$)/)[1].toLowerCase();
    };
    $scope.getDisambiguations = function() {
      return $http({
        "method": "GET",
        "url": "admin-ajax.php",
        "params": {
          "action": "wordlift.disambiguation",
          "postID": postID
        }
      }).success(function(data) {
        return $scope.disambiguations = data;
      });
    };
    $scope.$on("job-complete", function() {
      return $scope.getDisambiguations();
    });
    return $scope.getDisambiguations();
  }).controller("JobController", function($scope, $rootScope, $http, $timeout) {
    var postID;
    postID = $("#post_ID").val();
    $scope.getJob = function() {
      return $http({
        "method": "GET",
        "url": "admin-ajax.php",
        "params": {
          "action": "wordlift.job",
          "postID": postID
        }
      }).success(function(data, status, headers, config) {
        return $scope.state = data.jobState;
      }).error(function(data, status, headers, config) {
        return console.log(status);
      });
    };
    $scope.postJob = function() {
      $scope.state = "running";
      return $http({
        "method": "POST",
        "url": "admin-ajax.php",
        "params": {
          "action": "wordlift.job",
          "postID": postID
        }
      }).success(function(data, status, headers, config) {
        return $scope.state = "running";
      }).error(function(data, status, headers, config) {
        return $scope.state = "error";
      });
    };
    $scope.isRunning = function() {
      return "running" === $scope.state;
    };
    $scope.$watch("state", function() {
      if ($scope.isRunning()) {
        return $scope.watchJob();
      }
    });
    $scope.watchJob = function() {
      if (!$scope.isRunning()) {
        return $rootScope.$broadcast("job-complete");
      }
      return $timeout(function() {
        $scope.getJob();
        return $scope.watchJob();
      }, 5000);
    };
    return $scope.getJob();
  });

  angular.bootstrap($("#wordlift-disambiguate"), ["wordlift.disambiguate"]);

}).call(this);
