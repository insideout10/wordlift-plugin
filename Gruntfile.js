'use strict';

module.exports = function ( grunt ) {

    var path = require( 'path' ),
        SOURCE_DIR = 'src/',
        BUILD_DIR = 'build/',
        config = {};

    // Load tasks.
    require( 'matchdep' ).filterDev( [ 'grunt-*', '!grunt-legacy-util' ] ).forEach( grunt.loadNpmTasks );

    // Load legacy utils
    grunt.util = require( 'grunt-legacy-util' );

    // This file isn't used anymore.
    // wordlift.js
    // config[ SOURCE_DIR + 'js/wordlift.js' ] = [
    //     SOURCE_DIR + 'coffee/traslator.coffee',
    //     SOURCE_DIR + 'coffee/app.constants.coffee',
    //     SOURCE_DIR + 'coffee/app.config.coffee',
    //     SOURCE_DIR + 'coffee/app.directives.wlEntityProps.coffee',
    //     SOURCE_DIR + 'coffee/app.directives.coffee',
    //     SOURCE_DIR + 'coffee/app.services.LoggerService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.AnalysisService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.EditorService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.EntityAnnotationService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.EntityAnnotationConfidenceService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.EntityService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.SearchService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.Helpers.coffee',
    //     SOURCE_DIR + 'coffee/app.services.TextAnnotationService.coffee',
    //     SOURCE_DIR + 'coffee/app.services.coffee',
    //     SOURCE_DIR + 'coffee/app.controllers.coffee',
    //     SOURCE_DIR + 'coffee/app.coffee',
    //     SOURCE_DIR + 'coffee/chordDialog.coffee'
    // ];

    // wordlift-reloaded.js
    config[ SOURCE_DIR + 'js/wordlift-reloaded.js' ] = [
        SOURCE_DIR + 'coffee/traslator.coffee',
        SOURCE_DIR + 'coffee/utils/app.utils.directives.coffee',
        SOURCE_DIR + 'coffee/ui/carousel.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.controllers.EditPostWidgetController.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlClassificationBox.coffee',
		SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlEntityList.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlEntityForm.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlEntityTile.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.directives.wlEntityInputBox.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.EditorAdapter.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.AnnotationParser.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.AnalysisService.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.EditorService.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.RelatedPostDataRetrieverService.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.services.GeoLocationService.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.providers.ConfigurationProvider.coffee',
        SOURCE_DIR + 'coffee/editpost-widget/app.coffee'
    ];

    // wordlift.ui.js
    config[ SOURCE_DIR + 'js/wordlift-ui.js' ] = [
        SOURCE_DIR + 'coffee/ui/chord.coffee',
        SOURCE_DIR + 'coffee/ui/timeline.coffee',
        SOURCE_DIR + 'coffee/ui/geomap.coffee',
        SOURCE_DIR + 'coffee/ui/carousel.coffee',
        SOURCE_DIR + 'coffee/utils/app.utils.directives.coffee',
        SOURCE_DIR + 'coffee/navigator-widget/app.coffee'
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
                SOURCE_DIR + '{js,css}/wordlift.*',
                SOURCE_DIR + '{js,css}/wordlift-faceted-entity-search-widget.*',
                SOURCE_DIR + '{js,css}/wordlift-reloaded.*',
                SOURCE_DIR + '{js,css}/wordlift-ui.*'
            ],
            dynamic: {
                dot: true,
                expand: true,
                cwd: BUILD_DIR,
                src: []
            }
        },
        /* CoffeeScript compilation */
        _coffee: {
            compile: {
                options: {
                    join: false,
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
                    // 'js/wordlift.js',
                    'js/wordlift-reloaded.js',
                    'js/wordlift-ui.js',
                    'js/wordlift-faceted-entity-search-widget.js'
                ]
            }
        },
        /* CSS */
        _less: {
            all: {
                expand: true,
                cwd: SOURCE_DIR + 'less/',
                dest: SOURCE_DIR + 'css/',
                ext: '.css',
                src: [
                    'wordlift.less',
                    'wordlift-ui.less',
                    'wordlift-reloaded.less',
                    'wordlift-faceted-entity-search-widget.less'
                ]
            }
        },
        autoprefixer: {
          options: {
            browsers: ['last 20 versions', 'ie 8', 'ie 9']          },
          main: {
            expand: true,
            flatten: true,
            cwd: SOURCE_DIR + 'css/',
            src: ['*.css', '!*.min.css'],
            dest: SOURCE_DIR + 'css/',
          }
        },
        /* Minify css */
        cssmin: {
            all: {
                expand: true,
                cwd: SOURCE_DIR + 'css/',
                dest: SOURCE_DIR + 'css/',
                ext: '.min.css',
                src: ['*.css', '!*.min.css']
            }
        },
        /* Copy files */
        copy: {
            /* Copy font files */
            fonts: {
                expand: true,
                cwd: SOURCE_DIR + 'bower_components/components-font-awesome/fonts/',
                src: '*',
                dest: SOURCE_DIR + 'fonts/',
                flatten: true,
                filter: 'isFile'
            },
            build: {
                files: [
                    {
                        dot: true,
                        expand: true,
                        cwd: SOURCE_DIR,
                        src: [
                            '**',
                            '!**/.{svn,git}/**', // Ignore version control directories.
                            // Ignore unminified versions of external libs we don't ship:
                            '!coffee/**',
                            '!less/**'
                        ],
                        dest: BUILD_DIR
                    }
                ]
            },
            dynamic: {
                dot: true,
                expand: true,
                cwd: SOURCE_DIR,
                dest: BUILD_DIR,
                src: []
            }
        },
        /* Watch for changes */
        watch: {
            /* Enable when using a build folder */
            all: {
               files: [
                   SOURCE_DIR + '**',
                   // Ignore version control directories.
                   '!' + SOURCE_DIR + '**/.{svn,git}/**',
                   '!' + SOURCE_DIR + 'less/**',
                   '!' + SOURCE_DIR + 'coffee/**',
                   '!' + SOURCE_DIR + '**/*.coffee',
                   '!' + SOURCE_DIR + '**/*.less',
               ],
               //tasks: [ 'clean:dynamic', 'copy:dynamic' ],
               options: {
                   dot: true,
                   spawn: false,
                   interval: 2000,
                   livereload: true,
               }
            },
            coffee: {
                files: [ SOURCE_DIR + 'coffee/**/*.coffee' ],
                tasks: [ 'coffee' ]
            },
            less: {
                files: [ SOURCE_DIR + 'less/**/*.less' ],
                tasks: [ 'less' ]
            },
            autoprefixer: {
              files: [ SOURCE_DIR + 'css/**/*.css' ],
              tasks: [ 'autoprefixer' ]
            },
            config: {
                files: 'Gruntfile.js'
            }
        },
        phpunit: {
            'default': {
                cmd: 'phpunit',
                args: [ '-c', 'phpunit.xml' ]
            }
        },
        /* Karma JS Unit Testing */
        karma: {
            unit: {
                configFile: 'tests/js/config/karma.conf.js',
                singleRun: true,
                reporters: 'dots'
            }
        }
    } );

    /* Rename the coffee task in _coffee, in order to create a coffee task that
     * includes both coffee and uglify, so that every time that coffee generates
     * a JavaScript file, it is also minified.
     */
    grunt.renameTask( 'coffee', '_coffee' );
    grunt.registerTask( 'coffee', [ '_coffee', 'uglify' ] );

    grunt.renameTask( 'less', '_less' );
    grunt.registerTask( 'less', [ '_less', 'cssmin' ] );

    grunt.registerTask( 'build', [
        'coffee',
        'less',
        'autoprefixer',
        'cssmin',
        'copy:fonts',
        'copy:build'
    ] );

    grunt.registerTask( 'rebuild', [
        'clean',
        'build'
    ] );


    /*
     * Automatically updates the `:dynamic` configurations
     * so that only the changed files are updated.
     */
    //grunt.event.on( 'watch', function ( action, filepath, target ) {
    //    var src;
    //
    //    src = [ path.relative( SOURCE_DIR, filepath ) ];
    //
    //    grunt.log.writeln( '[ action :: %s ][ filepath :: %s ][ target :: %s ][ source :: %s ]', action, filepath, target, src[ 0 ] );
    //
    //    //if ( [ 'coffee', 'less' ].indexOf( target ) > -1 ) {
    //    //    return;
    //    //}
    //
    //    if ( action === 'deleted' ) {
    //        grunt.config( [ 'clean', 'dynamic', 'src' ], src );
    //    } else {
    //        grunt.config( [ 'copy', 'dynamic', 'src' ], src );
    //    }
    //} );

    // Testing tasks.
    grunt.registerMultiTask( 'phpunit', 'Runs PHPUnit tests.', function () {
        grunt.util.spawn( {
            cmd: this.data.cmd,
            args: this.data.args,
            opts: { stdio: 'inherit' }
        }, this.async() );
    } );

    grunt.registerTask( 'test', 'Runs all PHPUnit tasks.', [ 'phpunit' ] );

    // Travis CI tasks.
    grunt.registerTask( 'travis:js', 'Runs Javascript Travis CI tasks.', 'karma' );
    grunt.registerTask( 'travis:phpunit', 'Runs PHPUnit Travis CI tasks.', 'phpunit' );

    grunt.registerTask( 'default', [ 'build' ] );

};
