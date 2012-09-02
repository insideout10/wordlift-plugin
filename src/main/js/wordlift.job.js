(function() {
  var $, app;

  $ = jQuery;

  app = angular.module("wordlift.job", []);

  app.controller("JobCtrl", function($scope, $rootScope, $http, $timeout) {
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

  angular.bootstrap($("#wordlift-job"), ["wordlift.job"]);

}).call(this);
