'use strict';

module.exports = function ( grunt ) {

    var SOURCE_DIR = 'src/',
        BUILD_DIR = 'build/',
        config = {};

    // wordlift.js
    config[ SOURCE_DIR + 'js/wordlift.js' ] = [
        SOURCE_DIR + 'coffee/traslator.coffee',
        SOURCE_DIR + 'coffee/app.constants.coffee',
        SOURCE_DIR + 'coffee/app.config.coffee',
        SOURCE_DIR + 'coffee/app.directives.wlEntityProps.coffee',
        SOURCE_DIR + 'coffee/app.directives.coffee',
        SOURCE_DIR + 'coffee/app.services.LoggerService.coffee',
        SOURCE_DIR + 'coffee/app.services.AnalysisService.coffee',
        SOURCE_DIR + 'coffee/app.services.EditorService.coffee',
        SOURCE_DIR + 'coffee/app.services.EntityAnnotationService.coffee',
        SOURCE_DIR + 'coffee/app.services.EntityAnnotationConfidenceService.coffee',
        SOURCE_DIR + 'coffee/app.services.EntityService.coffee',
        SOURCE_DIR + 'coffee/app.services.SearchService.coffee',
        SOURCE_DIR + 'coffee/app.services.Helpers.coffee',
        SOURCE_DIR + 'coffee/app.services.TextAnnotationService.coffee',
        SOURCE_DIR + 'coffee/app.services.coffee',
        SOURCE_DIR + 'coffee/app.controllers.coffee',
        SOURCE_DIR + 'coffee/app.coffee',
        SOURCE_DIR + 'coffee/chordDialog.coffee'
    ];

    // wordlift-reloaded.js
    config[ SOURCE_DIR + 'js/wordlift-reloaded.js' ] = [
        SOURCE_DIR + 'coffee/traslator.coffee',
        SOURCE_DIR + 'coffee/utils/app.utils.directives.coffee',
        SOURCE_DIR + 'coffee/ui/carousel.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.controllers.EditPostWidgetController.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlClassificationBox.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlEntityForm.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlEntityTile.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlEntityInputBox.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.AnalysisService.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.EditorService.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.RelatedPostDataRetrieverService.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.providers.ConfigurationProvider.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.coffee'
    ];

    // wordlift.ui.js
    config[ SOURCE_DIR + 'js/wordlift.ui.js' ] = [
        SOURCE_DIR + 'coffee/ui/chord.coffee',
        SOURCE_DIR + 'coffee/ui/timeline.coffee',
        SOURCE_DIR + 'coffee/ui/geomap.coffee'
    ];

    // wordlift-faceted-entity-search-widget.js
    config[ SOURCE_DIR + 'js/wordlift-faceted-entity-search-widget.js' ] = [
        SOURCE_DIR + 'coffee/ui/carousel.coffee',
        SOURCE_DIR + 'coffee/utils/app.utils.directives.coffee',
        SOURCE_DIR + 'coffee/faceted-entity-search-widget/app.coffee'
    ];

    grunt.initConfig( {
        pkg: grunt.file.readJSON( 'package.json' ),
        /* CoffeeScript compilation */
        coffee: {
            compile: {
                options: {
                    join: true,
                    sourceMap: true
                },
                files: config
            }
        },
        /* Minify the JavaScript files */
        uglify: {
            'wordlift': {
                options: {
                    sourceMap: true,
                    sourceMapIn: SOURCE_DIR + 'js/wordlift.js.map',
                    compress: {},
                    drop_console: true,
                    dead_code: true,
                    mangle: true,
                    beautify: false
                },
                files: {
                    'app/js/wordlift.min.js': 'app/js/wordlift.js'
                }
            },
            'wordlift-ui': {
                options: {
                    sourceMap: true,
                    sourceMapIn: 'app/js/wordlift.ui.js.map',
                    compress: {},
                    drop_console: true,
                    dead_code: true,
                    mangle: true,
                    beautify: false
                },
                files: {
                    'app/js/wordlift.ui.min.js': 'app/js/wordlift.ui.js'
                }
            },
            'wordlift-reloaded': {
                options: {
                    sourceMap: true,
                    sourceMapIn: 'app/js/wordlift-reloaded.js.map',
                    compress: {},
                    drop_console: true,
                    dead_code: true,
                    mangle: true,
                    beautify: false
                },
                files: {
                    'app/js/wordlift-reloaded.min.js': 'app/js/wordlift-reloaded.js'
                }
            },
            'wordlift-faceted-entity-search-widget': {
                options: {
                    sourceMap: true,
                    sourceMapIn: 'app/js/wordlift-faceted-entity-search-widget.js.map',
                    compress: {},
                    drop_console: true,
                    dead_code: true,
                    mangle: true,
                    beautify: false
                },
                files: {
                    'app/js/wordlift-faceted-entity-search-widget.min.js': 'app/js/wordlift-faceted-entity-search-widget.js'
                }
            }
        },
        /* CSS */
        less: {
            development: {
                files: {
                    'app/css/wordlift.css': [ SOURCE_DIR + 'less/wordlift.less' ],
                    'app/css/wordlift.ui.css': [ SOURCE_DIR + 'less/wordlift.ui.less' ],
                    'app/css/wordlift-reloaded.css': [ SOURCE_DIR + 'less/wordlift-reloaded.less' ],
                    'app/css/wordlift-faceted-entity-search-widget.css': [ SOURCE_DIR + 'less/wordlift-faceted-entity-search-widget.less' ]
                }
            },
            dist: {
                options: {
                    cleancss: true,
                    sourceMap: true,
                    sourceMapFilename: 'app/css/wordlift.min.css.map'
                },
                files: {
                    'app/css/wordlift.min.css': SOURCE_DIR + 'less/wordlift.less',
                    'app/css/wordlift.ui.min.css': SOURCE_DIR + 'less/wordlift.ui.less',
                    'app/css/wordlift-reloaded.min.css': SOURCE_DIR + 'less/wordlift-reloaded.less',
                    'app/css/wordlift-faceted-entity-search-widget.min.css': SOURCE_DIR + 'less/wordlift-faceted-entity-search-widget.less'
                }
            }
        },
        /* Copy files */
        copy: {
            fonts: {
                expand: true,
                cwd: 'bower_components/components-font-awesome/fonts/',
                src: '*',
                dest: 'app/fonts/',
                flatten: true,
                filter: 'isFile'
            },
            'dist-scripts': {
                expand: true,
                cwd: 'app/js/',
                src: [ 'wordlift-faceted-entity-search-widget.js',
                    'wordlift-faceted-entity-search-widget.js.map',
                    'wordlift-faceted-entity-search-widget.min.js',
                    'wordlift-faceted-entity-search-widget.min.map',
                    'wordlift-reloaded.js',
                    'wordlift-reloaded.js.map',
                    'wordlift-reloaded.min.js',
                    'wordlift-reloaded.min.map',
                    'wordlift.js',
                    'wordlift.js.map',
                    'wordlift.min.js',
                    'wordlift.min.map',
                    'wordlift.ui.js',
                    'wordlift.ui.js.map',
                    'wordlift.ui.min.js',
                    'wordlift.ui.min.map' ],
                dest: 'dist/<%= pkg.version %>/js/',
                flatten: true
            },
            'dist-stylesheets': {
                expand: true,
                cwd: 'app/css/',
                src: [ 'wordlift-faceted-entity-search-widget.css',
                    'wordlift-faceted-entity-search-widget.min.css',
                    'wordlift-faceted-entity-search-widget.min.css.map',
                    'wordlift-reloaded.css',
                    'wordlift-reloaded.min.css',
                    'wordlift-reloaded.min.css.map',
                    'wordlift.css',
                    'wordlift.min.css',
                    'wordlift.min.css.map',
                    'wordlift.ui.css',
                    'wordlift.ui.min.css',
                    'wordlift.ui.min.css.map' ],
                dest: 'dist/<%= pkg.version %>/css/',
                flatten: true
            },
            'dist-fonts': {
                expand: true,
                cwd: 'app/fonts/',
                src: '*',
                dest: 'dist/<%= pkg.version %>/fonts/',
                flatten: true
            }
        },
        symlink: {
            options: {
                overwrite: true
            },
            explicit: {
                src: 'dist/<%= pkg.version %>',
                dest: 'dist/latest'
            }
        },
        /* Document file */
        docco: {
            doc: {
                src: [ SOURCE_DIR + 'coffee/**/*.coffee',
                    'test/unit/**/*.coffee' ],
                options: {
                    output: 'docs/'
                }
            }
        },
        /* Watch for changes */
        watch: {
            scripts: {
                files: [ SOURCE_DIR + 'coffee/**/*.coffee' ],
                tasks: [ 'coffee',
                    'uglify',
                    'copy:dist-scripts',
                    'docco' ],
                options: {
                    spawn: false
                }
            },
            styles: {
                files: [ SOURCE_DIR + 'less/*.less' ],
                tasks: [ 'less',
                    'copy:dist-fonts',
                    'copy:dist-stylesheets' ],
                options: {
                    spawn: false
                }
            }
        }
    } );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-contrib-coffee' );
    grunt.loadNpmTasks( 'grunt-contrib-less' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-contrib-symlink' );
    grunt.loadNpmTasks( 'grunt-docco' );
    return grunt.registerTask( 'default',
        [ 'coffee',
            'uglify',
            'less',
            'copy',
            'symlink',
            'docco' ] );
};