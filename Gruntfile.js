module.exports = function(grunt) {

    grunt.initConfig({
        phpunit: {
            classes: {
                dir: 'tests'
            },
            options: {
                bin: 'vendor/bin/phpunit',
                bootstrap: 'vendor/autoload.php',
                colors: true
            }
        },
        phpcsfixer: {
            src: {
                dir: 'src/*'
            },
            options: {

            }
        },
        watch: {
            test: {
                files: ['src/**/*', 'tests/**/*'],
                tasks: ['phpcsfixer']
            }
         }
    });

    require('load-grunt-tasks')(grunt);

    grunt.registerTask('default', ['phpcsfixer:src']);
};