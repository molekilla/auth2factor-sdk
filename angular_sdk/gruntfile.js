/*global module:false*/
module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        // Metadata.
        pkg: grunt.file.readJSON('package.json'),
        banner: '/*! <%= pkg.title || pkg.name %> - v<%= pkg.version %> - ' +
            '<%= grunt.template.today("yyyy-mm-dd") %>\n' +
            '<%= pkg.homepage ? "* " + pkg.homepage + "\\n" : "" %>' +
            '* Copyright (c) <%= grunt.template.today("yyyy") %> <%= pkg.author.name %>;' +
            ' Licensed <%= _.pluck(pkg.licenses, "type").join(", ") %> */\n',
        // Task configuration.
        concat: {
            options: {
                banner: '<%= banner %>',
                stripBanners: true
            },
            dist: {
                src: ['lib/<%= pkg.title || pkg.name %>.js'],
                dest: 'dist/<%= pkg.title || pkg.name %>.js'
            }
        },
        uglify: {
            options: {
                banner: '<%= banner %>'
            },
            dist: {
                src: '<%= concat.dist.dest %>',
                dest: 'dist/<%= pkg.name %>.min.js'
            }
        },
        jshint: {
            options: {
                curly: true,
                eqeqeq: true,
                immed: true,
                latedef: true,
                newcap: true,
                noarg: true,
                sub: true,
                undef: true,
                unused: true,
                boss: true,
                eqnull: true,
                browser: true,
                globals: {}
            },
            gruntfile: {
                src: 'Gruntfile.js'
            },
            lib_test: {
                src: ['lib/**/*.js', 'test/**/*.js']
            }
        },
        ngAnnotate: {
            options: {
                singleQuotes: true,
                ngAnnotateOptions: {}
            },
            dev: {
                files: {
                    'dist/app.js': ['lib/**/*.js']
                }
            }
        },
        babel: {
            es6: {
                files: [
                    {
                        expand: true,
                        src: ["lib/**/*.es6"],
                        ext: ".js"
        }
    ],
                options: {}
            }
        },
        concurrent: {
            target: {
                tasks: ['nodemon', 'copy', 'watch', 'http-server'],
                options: {
                    logConcurrentOutput: true
                }
            }
        },
        copy: {
            main: {
                cwd: 'lib',
                expand: true,
                src: ['*.js'],
                dest: 'public/app/services/',
                flatten: false,
                filter: 'isFile'                  
            }
        },
        nodemon: {
            dev: {
                options: {
                    //file: 'app.js',
                    nodeArgs: ['--debug']
                }
            }
        },
        watch: {
            gruntfile: {
                files: '<%= jshint.gruntfile.src %>',
                tasks: ['jshint:gruntfile']
            },
            frontEndES6: {
                files: ['lib/**/*.es6'],
                tasks: ['babel:es6']
            },
            ui: {
                files: ['lib/**/*.js', 'spec/**/*.js'],
                tasks: ['babel:es6']
            }
        },
        'http-server': {

            'dev': {

                // the server root directory 
                root: 'public',

                // the server port 
                // can also be written as a function, e.g. 
                // port: function() { return 8282; } 
                port: 3005,


                // the host ip address 
                // If specified to, for example, "127.0.0.1" the server will  
                // only be available on that ip. 
                // Specify "0.0.0.0" to be available everywhere 
                host: "127.0.0.1",

                cache: 0,
                showDir: true,
                autoIndex: true,

                // server default file extension 
                ext: "html",

                // run in parallel with other tasks 
                runInBackground: false,

                // specify a logger function. By default the requests are 
                // sent to stdout. 
                logFn: function (req, res, error) {}


            }

        }
    });

    grunt.loadNpmTasks('grunt-ng-annotate');
    grunt.loadNpmTasks('grunt-http-server');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-babel');

    grunt.loadNpmTasks('grunt-nodemon');
    grunt.loadNpmTasks('grunt-concurrent');

    grunt.registerTask('serve', ['concurrent:target']);
    // grunt.registerTask('build', ['babel:es6', 'ngAnnotate']);



};