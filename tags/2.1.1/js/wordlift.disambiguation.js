(function() {
  var $, app;

  $ = jQuery;

  app = angular.module("wordlift.metabox", []);

  app.filter("confidence", function() {
    return function(entities, confidence) {
      var entity, _i, _len, _results;
      if (!(entities != null)) {
        return;
      }
      _results = [];
      for (_i = 0, _len = entities.length; _i < _len; _i++) {
        entity = entities[_i];
        if (entity.confidence === confidence) {
          _results.push(entity);
        }
      }
      return _results;
    };
  });

  app.filter("related", function() {
    return function(entities, parent) {
      var entity, _i, _len, _results;
      if (!(entities != null)) {
        return;
      }
      _results = [];
      for (_i = 0, _len = entities.length; _i < _len; _i++) {
        entity = entities[_i];
        if (entity.texts.some(function(text) {
          return ~parent.texts.indexOf(text);
        })) {
          _results.push(entity);
        }
      }
      return _results;
    };
  });

  app.controller("EntitiesCtrl", function($scope, $http, $filter) {
    var images, postID;
    postID = $("#post_ID").val();
    images = {
      "http://schema.org/Place": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_462.svg",
      "http://schema.org/Organization": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_921.svg",
      "http://schema.org/CreativeWork": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_2572.svg",
      "http://schema.org/Person": "https://dl.dropbox.com/u/8801031/WordLift/icons/noun_project_419%202.svg"
    };
    $scope.getEntities = function() {
      return $http({
        "method": "GET",
        "url": "admin-ajax.php",
        "params": {
          "action": "wordlift.dump",
          "postID": postID
        }
      }).success(function(data) {
        return $scope.entities = data;
      });
    };
    $scope.getImageURL = function(type) {
      return images[type];
    };
    $scope.bindEntity = function(entity, box) {
      var method;
      box.binding = true;
      method = entity.selected ? "DELETE" : "POST";
      return $http({
        "method": method,
        "url": "admin-ajax.php",
        "params": {
          "action": "wordlift.post/entities",
          "postID": postID,
          "entity": entity.entity
        },
        "data": {
          "texts": entity.texts
        }
      }).success(function(data, status, headers, config) {
        var e, _i, _len, _ref;
        _ref = $filter("related")($scope.entities, box);
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          e = _ref[_i];
          e.selected = false;
        }
        entity.selected = method === "POST";
        return box.binding = false;
      }).error(function(data, status, headers, config) {
        return box.binding = false;
      });
    };
    $scope.getEntities();
    return $scope.$on("job-complete", function() {
      console.log("job-complete");
      return $scope.getEntities();
    });
  });

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

  angular.bootstrap($("#wordlift-metabox"), ["wordlift.metabox"]);

}).call(this);
