(function() {
  describe("Test di prova", function() {
    var scope;
    beforeEach(module("wordlift.tinymce.plugin"));
    beforeEach(module("wordlift.tinymce.plugin.config"));
    beforeEach(module("wordlift.tinymce.plugin.services"));
    beforeEach(module("wordlift.tinymce.plugin.controllers"));
    describe("HelloController", function() {});
    scope = null;
    beforeEach(inject(function($controller, $rootScope) {
      scope = $rootScope.$new();
      $controller('HelloController', {
        $scope: scope
      });
    }));
    return it("Tests if scope.annotations is a blank array", function() {
      expect(scope.annotations).toEqual([]);
    });
  });

  describe("Test configuration dependency", function() {
    var configuration, service;
    service = null;
    configuration = null;
    angular.module("wordlift.unittest", ["wordlift.tinymce.plugin.config"]).service("MockService", [
      'Configuration', function(Configuration) {
        return {
          getConfig: function() {
            return Configuration;
          }
        };
      }
    ]);
    beforeEach(function() {
      module("wordlift.tinymce.plugin.config");
      module("wordlift.unittest");
      return inject(function(MockService, Configuration) {
        service = MockService;
        return configuration = Configuration;
      });
    });
    return it("Tests if configuration object is available within the service", function() {
      expect(service.getConfig()).toEqual(configuration);
    });
  });

}).call(this);

/*
//@ sourceMappingURL=wordlift-tinymce-plugin-test-suite.js.map
*/