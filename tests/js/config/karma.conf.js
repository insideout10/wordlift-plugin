module.exports = function (config) {
  var SOURCE_DIR = "../../src/";

  config.set({
    basePath: "../",

    preprocessors: {
      "**/*.html": [],
      "**/*.coffee": ["coffee"],
    },

    files: [
      // Serve TinyMCE scripts.
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
        pattern: "app/lib/jquery-ui-1.10.3/themes/base/images/*.png",
        watched: false,
        served: true,
        included: false,
      },
      {
        pattern: "app/lib/leaflet-0.7.3/images/*.png",
        watched: false,
        served: true,
        included: false,
      },

      // Serve HTML files.
      {
        pattern: "app/**/*.html",
        watched: false,
        served: true,
        included: false,
      },

      // Serve JSON files.
      {
        pattern: "app/**/*.json",
        watched: false,
        served: true,
        included: false,
      },

      // Serve TXT files
      {
        pattern: "app/**/*.txt",
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
      "app/lib/leaflet-0.7.3/leaflet.css",
      "app/lib/d3/d3.js",
      "app/lib/timelinejs/js/timeline-min.js",
      "app/lib/timelinejs/css/timeline.css",
      "app/lib/wordpress/iris.min.js",
      "app/lib/wordpress/color-picker.min.js",
      SOURCE_DIR + "js/wordlift-reloaded.min.js",
      SOURCE_DIR + "js/wordlift-ui.min.js",

      "test/lib/angular/angular-mocks.js",
      "test/unit/**/*.coffee",
      "app/lib/jquery-ui-1.10.3/themes/base/*.css",
    ],

    exclude: [
      "app/lib/angular/angular-loader.js",
      "app/lib/angular/*.min.js",
      "app/lib/angular/angular-scenario.js",
    ],

    autoWatch: true,

    frameworks: ["jasmine"],

    browsers: ["chrome_no_sandbox", "Firefox"],

    customLaunchers: {
      chrome_no_sandbox: {
        base: "Chrome",
        flags: ["--no-sandbox"],
      }
    },

    phantomjsLauncher: {
      // Have phantomjs exit if a ResourceError is encountered (useful if karma exits without killing phantom)
      exitOnResourceError: true,
    },

    plugins: [
      "karma-chrome-launcher",
      "karma-coffee-preprocessor",
      "karma-firefox-launcher",
      "karma-safari-launcher",
      "karma-html2js-preprocessor",
      "karma-jasmine",
      "karma-junit-reporter",
      "karma-phantomjs-launcher",
    ],

    junitReporter: {
      outputFile: "test_out/unit.xml",
      suite: "unit",
    },
  });

  if (process.env.TRAVIS) {
    config.browsers = ["Chrome_travis_ci"];
  }
};
