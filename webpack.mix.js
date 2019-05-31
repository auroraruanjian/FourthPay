const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css');


if( mix.inProduction()){
    mix.version([]);
}

mix.extract([
    'vue',
    'vuex',
    'vue-router',
    'axios'
]);

const webpack_config = {
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js')
        }
    },
};

mix.webpackConfig(webpack_config);