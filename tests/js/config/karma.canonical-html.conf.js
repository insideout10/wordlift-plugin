module.exports = function (config) {
  var SOURCE_DIR = "../../src/coffee/";

  config.set({
    basePath: "../",

    preprocessors: {
      "**/*.coffee": ["coffee"],
      "../../src/coffee/**/*.coffee": ["coffee"],
    },

    files: [
      {
        pattern: "app/lib/tinymce/jscripts/tiny_mce/plugins/**/*.js",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/lib/tinymce/jscripts/tiny_mce/plugins/**/*.css",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/lib/tinymce/jscripts/tiny_mce/themes/**/*.js",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/lib/tinymce/jscripts/tiny_mce/themes/**/*.css",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/lib/tinymce/jscripts/tiny_mce/themes/**/*.gif",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/lib/tinymce/jscripts/tiny_mce/themes/**/*.png",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/lib/tinymce/jscripts/tiny_mce/langs/**/*.js",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/**/*.html",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/**/*.json",
        watched: false,
        served: true,
        included: false,
      },
      "app/lib/jquery/jquery-1.10.2.min.js",
      "app/lib/angular/angular.js",
      "app/lib/angular/angular-*.js",
      "app/lib/jquery-ui-1.10.3/ui/jquery-ui.js",
      "app/lib/jquery-ui-1.10.3/ui/jquery.ui.dialog.js",
      "app/lib/jasmine-jquery.js",
      "app/lib/tinymce/jscripts/tiny_mce/tiny_mce.js",
      "app/js/test.init.coffee",
      "app/lib/leaflet-0.7.3/leaflet.js",
      "app/lib/d3/d3.js",
      "app/lib/timelinejs/js/timeline-min.js",
      "app/lib/wordpress/iris.min.js",
      "app/lib/wordpress/color-picker.min.js",
      SOURCE_DIR + "traslator.coffee",
      SOURCE_DIR + "editpost-widget/app.providers.ConfigurationProvider.coffee",
      SOURCE_DIR + "editpost-widget/app.services.AnnotationParser.coffee",
      SOURCE_DIR + "editpost-widget/app.services.EditorAdapter.coffee",
      SOURCE_DIR + "editpost-widget/app.services.AnalysisService.coffee",
      SOURCE_DIR + "editpost-widget/app.services.EditorService.coffee",
      "test/lib/angular/angular-mocks.js",
      "test/unit/EditorServiceCanonicalHtml.spec.coffee",
    ],

    exclude: [
      "app/lib/angular/angular-loader.js",
      "app/lib/angular/*.min.js",
      "app/lib/angular/angular-scenario.js",
    ],

    frameworks: ["jasmine"],

    browsers: ["chrome_no_sandbox"],

    customLaunchers: {
      chrome_no_sandbox: {
        base: "Chrome",
        flags: ["--no-sandbox"],
      },
    },

    plugins: [
      "karma-chrome-launcher",
      "karma-coffee-preprocessor",
      "karma-html2js-preprocessor",
      "karma-jasmine",
    ],

    singleRun: true,
    autoWatch: false,
  });
};
