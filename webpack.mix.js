const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');

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

mix.sourceMaps();

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
    output: {
        chunkFilename: '[name].js',
    },
    optimization: {
        splitChunks: {
            //chunks: 'all'
        }
    },
    plugins: [
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['!favicon.ico','!index.php','!robots.txt','!.htaccess','*.js','*.css'],
            cleanAfterEveryBuildPatterns: ['!favicon.ico','!index.php','!robots.txt','!.htaccess','*.js','*.css'],
        })
    ]
};

mix.webpackConfig(webpack_config);