const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const ChunkRenamePlugin = require("webpack-chunk-rename-plugin");

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

mix.js('resources/js/app.js','')
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
],'vendor');

const webpack_config = {
    resolve: {
        alias: {
            'res': path.resolve(__dirname, 'resources'),
            '@': path.resolve(__dirname, 'resources/js'),
        }
    },
    output: {
        filename: (chunkData) => {
            return 'js/' + chunkData.chunk.name.replace(/\//g, '') + '.js';
        },
        chunkFilename: 'js/[name].bundle.js?[chunkhash]',
    },
    optimization: {
        splitChunks: {
            //chunks: 'all'
        }
    },
    module: {
        rules: [
        ]
    },
    plugins: [
        new CleanWebpackPlugin({
            cleanOnceBeforeBuildPatterns: ['!favicon.ico','!index.php','!robots.txt','!.htaccess','*.js','*.css'],
            cleanAfterEveryBuildPatterns: ['!favicon.ico','!index.php','!robots.txt','!.htaccess','*.js','*.css'],
        }),
        new ChunkRenamePlugin({
            initialChunksWithEntry: true,
            '/vendor': 'js/vendor.js'
        }),
    ]
};

Mix.listen('configReady', (webpackConfig) => {
    // Create SVG sprites
    webpackConfig.module.rules.unshift({
        test: /\.svg$/,
        loader: 'svg-sprite-loader',
        include: [path.resolve(__dirname,'resources/js/icons/svg')],
        options: {
            symbolId: 'icon-[name]'
        }
    });

    // Exclude 'svg' folder from font loader
    let fontLoaderConfig1 = webpackConfig.module.rules.find(rule => String(rule.test) === String(/(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/));
    fontLoaderConfig1.exclude = path.resolve('resources/js/icons/svg');

    let fontLoaderConfig2 = webpackConfig.module.rules.find(rule => String(rule.test) === String(/(\.(woff2?|ttf|eot|otf)$|font.*\.svg$)/));
    fontLoaderConfig2.exclude = path.resolve('resources/js/icons/svg');///(resources\/js\/icons\/svg)/;
});

mix.copy('resources/css/weui.css', 'public/css/weui.css');

mix.webpackConfig(webpack_config);