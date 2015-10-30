'use strict'

module.exports = (grunt) ->

  # Project configuration.
  grunt.initConfig
    pkg: grunt.file.readJSON('package.json')

    coffee:
      compile:
        options:
          join: true
          sourceMap: true
        files:
          'app/js/wordlift.js': [
            'src/coffee/traslator.coffee'
            'src/coffee/app.constants.coffee'
            'src/coffee/app.config.coffee'
            'src/coffee/app.directives.wlEntityProps.coffee'
            'src/coffee/app.directives.coffee'
            'src/coffee/app.services.LoggerService.coffee'
            'src/coffee/app.services.AnalysisService.coffee'
            'src/coffee/app.services.EditorService.coffee'
            'src/coffee/app.services.EntityAnnotationService.coffee'
            'src/coffee/app.services.EntityAnnotationConfidenceService.coffee'
            'src/coffee/app.services.EntityService.coffee'
            'src/coffee/app.services.SearchService.coffee'
            'src/coffee/app.services.Helpers.coffee'
            'src/coffee/app.services.TextAnnotationService.coffee'
            'src/coffee/app.services.coffee'
            'src/coffee/app.controllers.coffee'
            'src/coffee/app.coffee'
            'src/coffee/chordDialog.coffee'
          ]
          'app/js/wordlift-reloaded.js': [
            'src/coffee/traslator.coffee'
            'src/coffee/utils/app.utils.directives.coffee'
            'src/coffee/ui/carousel.coffee'
            'src/coffee/editpost-widget/app.controllers.EditPostWidgetController.coffee'
            'src/coffee/editpost-widget/app.directives.wlClassificationBox.coffee'
            'src/coffee/editpost-widget/app.directives.wlEntityForm.coffee'
            'src/coffee/editpost-widget/app.directives.wlEntityTile.coffee'
            'src/coffee/editpost-widget/app.directives.wlEntityInputBox.coffee'
            'src/coffee/editpost-widget/app.services.AnalysisService.coffee'
            'src/coffee/editpost-widget/app.services.EditorService.coffee'
            'src/coffee/editpost-widget/app.services.RelatedPostDataRetrieverService.coffee'
            'src/coffee/editpost-widget/app.providers.ConfigurationProvider.coffee'   
            'src/coffee/editpost-widget/app.coffee'
          ]
          'app/js/wordlift.ui.js': [
            'src/coffee/ui/chord.coffee'
            'src/coffee/ui/timeline.coffee'
            'src/coffee/ui/geomap.coffee'
          ]
          'app/js/wordlift-faceted-entity-search-widget.js': [
            'src/coffee/ui/carousel.coffee'
            'src/coffee/utils/app.utils.directives.coffee'
            'src/coffee/faceted-entity-search-widget/app.coffee'
          ]
    uglify:
      'wordlift':
        options:
          sourceMap: true
          sourceMapIn: 'app/js/wordlift.js.map'
          compress: {}
          drop_console: true
          dead_code: true
          mangle: true
          beautify: false
        files:
          'app/js/wordlift.min.js': 'app/js/wordlift.js'
      'wordlift-ui':
        options:
          sourceMap: true
          sourceMapIn: 'app/js/wordlift.ui.js.map'
          compress: {}
          drop_console: true
          dead_code: true
          mangle: true
          beautify: false
        files:
          'app/js/wordlift.ui.min.js': 'app/js/wordlift.ui.js'
      'wordlift-reloaded':
        options:
          sourceMap: true
          sourceMapIn: 'app/js/wordlift-reloaded.js.map'
          compress: {}
          drop_console: true
          dead_code: true
          mangle: true
          beautify: false
        files:
          'app/js/wordlift-reloaded.min.js': 'app/js/wordlift-reloaded.js'
      'wordlift-faceted-entity-search-widget':
        options:
          sourceMap: true
          sourceMapIn: 'app/js/wordlift-faceted-entity-search-widget.js.map'
          compress: {}
          drop_console: true
          dead_code: true
          mangle: true
          beautify: false
        files:
          'app/js/wordlift-faceted-entity-search-widget.min.js': 'app/js/wordlift-faceted-entity-search-widget.js'
    less:
      development:
        files:
          'app/css/wordlift.css': ['src/less/wordlift.less']
          'app/css/wordlift.ui.css': ['src/less/wordlift.ui.less']
          'app/css/wordlift-reloaded.css': ['src/less/wordlift-reloaded.less']
          'app/css/wordlift-faceted-entity-search-widget.css': ['src/less/wordlift-faceted-entity-search-widget.less']
      dist:
        options:
          cleancss: true
          sourceMap: true
          sourceMapFilename: 'app/css/wordlift.min.css.map'
        files:
          'app/css/wordlift.min.css': 'src/less/wordlift.less'
          'app/css/wordlift.ui.min.css': 'src/less/wordlift.ui.less'
          'app/css/wordlift-reloaded.min.css': 'src/less/wordlift-reloaded.less'
          'app/css/wordlift-faceted-entity-search-widget.min.css': 'src/less/wordlift-faceted-entity-search-widget.less'

    copy:
      fonts:
        expand: true
        cwd: 'bower_components/components-font-awesome/fonts/'
        src: '*'
        dest: 'app/fonts/'
        flatten: true
        filter: 'isFile'

      # Copy scripts to the dist folder.
      'dist-scripts':
        expand: true
        cwd: 'app/js/'
        src: [
          'wordlift-faceted-entity-search-widget.js'
          'wordlift-faceted-entity-search-widget.js.map'
          'wordlift-faceted-entity-search-widget.min.js'
          'wordlift-faceted-entity-search-widget.min.map'
          'wordlift-reloaded.js'
          'wordlift-reloaded.js.map'
          'wordlift-reloaded.min.js'
          'wordlift-reloaded.min.map'
          'wordlift.js'
          'wordlift.js.map'
          'wordlift.min.js'
          'wordlift.min.map'
          'wordlift.ui.js'
          'wordlift.ui.js.map'
          'wordlift.ui.min.js'
          'wordlift.ui.min.map'
        ]
        dest: 'dist/<%= pkg.version %>/js/'
        flatten: true

      # Copy stylesheets to the dist folder.
      'dist-stylesheets':
        expand: true
        cwd: 'app/css/'
        src: [
          'wordlift-faceted-entity-search-widget.css'
          'wordlift-faceted-entity-search-widget.min.css'
          'wordlift-faceted-entity-search-widget.min.css.map'
          'wordlift-reloaded.css'
          'wordlift-reloaded.min.css'
          'wordlift-reloaded.min.css.map'
          'wordlift.css'
          'wordlift.min.css'
          'wordlift.min.css.map'
          'wordlift.ui.css'
          'wordlift.ui.min.css'
          'wordlift.ui.min.css.map'
        ]
        dest: 'dist/<%= pkg.version %>/css/'
        flatten: true

      # Copy fonts to the dist folder.
      'dist-fonts':
        expand: true
        cwd: 'app/fonts/'
        src: '*'
        dest: 'dist/<%= pkg.version %>/fonts/'
        flatten: true

    symlink:
      options:
        overwrite: true
      explicit:
        src: 'dist/<%= pkg.version %>'
        dest: 'dist/latest'

    docco:
      doc:
        src: [
          'src/coffee/**/*.coffee',
          'test/unit/**/*.coffee',
        ]
        options:
          output: 'docs/'

    watch:
      scripts:
        files: ['src/coffee/**/*.coffee']
        tasks: ['coffee', 'uglify', 'copy:dist-scripts', 'docco']
        options:
          spawn: false
      styles:
        files: ['src/less/*.less']
        tasks: ['less', 'copy:dist-fonts', 'copy:dist-stylesheets']
        options:
          spawn: false

  # Load plugins
  grunt.loadNpmTasks('grunt-contrib-watch')
  grunt.loadNpmTasks('grunt-contrib-coffee')
  grunt.loadNpmTasks('grunt-contrib-less')
  grunt.loadNpmTasks('grunt-contrib-uglify')
  grunt.loadNpmTasks('grunt-contrib-copy')
  grunt.loadNpmTasks('grunt-contrib-symlink')
  grunt.loadNpmTasks('grunt-docco')

  # Default task(s).
  grunt.registerTask('default', ['coffee', 'uglify', 'less', 'copy', 'symlink', 'docco'])
