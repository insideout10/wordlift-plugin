(function() {
  describe("Test di prova", function() {
    var scope;
    beforeEach(module("wordlift.tinymce.plugin"));
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
    return it("Test if hello returns 'Ciao Marcello'", function() {
      expect(scope.hello).toBe("Ciao Marcello!");
    });
  });

}).call(this);

/*
//@ sourceMappingURL=wordlift-tinymce-plugin-test-suite.js.map
*/