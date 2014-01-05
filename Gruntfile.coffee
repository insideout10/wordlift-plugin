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
          'src/js/wordlift-tinymce-plugin.js': [
            'src/coffee/wordlift-tinymce-plugin/app.config.coffee',
            'src/coffee/wordlift-tinymce-plugin/app.services.coffee',
            'src/coffee/wordlift-tinymce-plugin/app.controllers.coffee',
            'src/coffee/wordlift-tinymce-plugin/app.coffee',
            'src/coffee/wordlift-tinymce-plugin.coffee'
          ]

    uglify:
      'wordlift-tinymce-plugin':
        options:
          sourceMap: 'src/js/wordlift-tinymce-plugin.min.js.map'
          sourceMapIn: 'src/js/wordlift-tinymce-plugin.js.map'
          sourceMappingURL: 'wordlift-tinymce-plugin.min.js.map'
          compress: false
          mangle: false
          beautify: true
        files:
          'src/js/wordlift-tinymce-plugin.min.js': ['src/js/wordlift-tinymce-plugin.js']

    less:
      development:
        files:
          'src/css/wordlift-admin.css' : ['src/less/wordlift-admin.less']
          'src/css/wordlift-editor.css': ['src/less/wordlift-editor.less']
      'wordlift-admin.min':
        options:
          cleancss: true
          sourceMap: true
          sourceMapFilename: 'src/css/wordlift-admin.min.css.map'
        files:
          'src/css/wordlift-admin.min.css': ['src/less/wordlift-admin.less']
      'wordlift-editor.min':
        options:
          cleancss: true
          sourceMap: true
          sourceMapFilename: 'src/css/wordlift-editor.min.css.map'
        files:
          'src/css/wordlift-editor.min.css': ['src/less/wordlift-editor.less']

    watch:
      scripts:
        files: ['src/coffee/**/*.coffee']
        tasks: ['coffee', 'uglify']
        options:
          spawn: false
      styles:
        files: ['src/less/*.less']
        tasks: ['less']
        options:
          spawn: false,

  # Load plugins
  grunt.loadNpmTasks('grunt-contrib-watch')
  grunt.loadNpmTasks('grunt-contrib-coffee')
  grunt.loadNpmTasks('grunt-contrib-less')
  grunt.loadNpmTasks('grunt-contrib-uglify')

  # Default task(s).
  grunt.registerTask('default', ['coffee', 'uglify', 'less'])
