module.exports = (grunt) ->

  # Project configuration.
  grunt.initConfig
    pkg: grunt.file.readJSON('package.json')

    coffee:
      compile:
        options:
          join: true
        files:
          'src/js/wordlift-tinymce-plugin.js': ['src/coffee/wordlift-tinymce-plugin.coffee']

    uglify:
      'wordlift-tinymce-plugin':
        files:
          'src/js/wordlift-tinymce-plugin.min.js': ['src/js/wordlift-tinymce-plugin.js']

    less:
      development:
        files:
          'src/css/wordlift-admin.css'    : ['src/less/wordlift-admin.less']
      production:
        options:
          cleancss: true
        files:
          'src/css/wordlift-admin.min.css': ['src/less/wordlift-admin.less']

    watch:
      scripts:
        files: ['src/coffee/*.coffee']
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
