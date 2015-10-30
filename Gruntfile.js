'use strict';

module.exports = function ( grunt ) {

    var SOURCE_DIR = 'src/',
        BUILD_DIR = 'build/',
        config = {};

    // Load tasks.
    require('matchdep').filterDev(['grunt-*', '!grunt-legacy-util']).forEach( grunt.loadNpmTasks );

    // Load legacy utils
    grunt.util = require('grunt-legacy-util');

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
    config[ SOURCE_DIR + 'js/wordlift-ui.js' ] = [
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
        /* Clean the build dir */
        clean: {
            all: [
                BUILD_DIR,
                SOURCE_DIR + 'js/wordlift.*',
                SOURCE_DIR + 'js/wordlift-faceted-entity-search-widget.*',
                SOURCE_DIR + 'js/wordlift-reloaded.*',
                SOURCE_DIR + 'js/wordlift-ui.*'
            ]
        },
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
            options: {
                ASCIIOnly: true
            },
            all: {
                expand: true,
                cwd: SOURCE_DIR,
                dest: SOURCE_DIR,
                ext: '.min.js',
                src: [
                    'js/wordlift.js',
                    'js/wordlift-reloaded.js',
                    'js/wordlift-ui.js',
                    'js/wordlift-faceted-entity-search-widget.js'
                ]
            }
        },
        /* CSS */
        less: {
            all: {
                expand: true,
                cwd: SOURCE_DIR + 'less/',
                dest: SOURCE_DIR + 'css/',
                src: [
                    'wordlift.less',
                    'wordlift-ui.less',
                    'wordlift-reloaded.less',
                    'wordlift-faceted-entity-search-widget.less'
                ]
            },
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
            files: {
                files: [
                    {
                        dot: true,
                        expand: true,
                        cwd: SOURCE_DIR,
                        src: [
                            '**',
                            '!**/.{svn,git}/**', // Ignore version control directories.
                            // Ignore unminified versions of external libs we don't ship:
                            '!coffee/**'
                        ],
                        dest: BUILD_DIR
                    }
                    //,{
                    //    src: 'wp-config-sample.php',
                    //    dest: BUILD_DIR
                    //}
                ]
            },
            fonts: {
                expand: true,
                cwd: 'bower_components/components-font-awesome/fonts/',
                src: '*',
                dest: 'app/fonts/',
                flatten: true,
                filter: 'isFile'
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

    grunt.registerTask( 'build', [
        'coffee',
        'uglify'
    ] );

    grunt.registerTask( 'rebuild', [
        'clean',
        'build'
    ] );

    return grunt.registerTask( 'default',
        [ 'coffee',
            'uglify',
            'less',
            'copy',
            'symlink',
            'docco' ] );
};