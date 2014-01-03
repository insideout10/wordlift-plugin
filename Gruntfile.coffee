module.exports = (grunt) ->

  # Project configuration.
  grunt.initConfig
    pkg: grunt.file.readJSON('package.json')

    coffee:
      compile:
        options:
          join: true
        files:
          'src/js/wordlift-tinymce.js': ['src/coffee/wordlift-tinymce.coffee']

    watch:
      scripts:
        files: ['src/coffee/*.coffee']
        tasks: ['coffee']
        options:
          spawn: false,

  # Load plugins
  grunt.loadNpmTasks('grunt-contrib-watch')
  grunt.loadNpmTasks('grunt-contrib-coffee')

  # Default task(s).
  grunt.registerTask('default', ['coffee'])
