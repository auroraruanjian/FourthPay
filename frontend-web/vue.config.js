const path = require('path');

module.exports = {
    productionSourceMap:false,
    devServer:{
        // proxy: {
        //     '/api': {
        //         target: 'http://api.laravel_admin.me',
        //         ws: true,
        //         changeOrigin: true,
        //         pathRewrite: {'^/api' : ''}
        //     },
        // }
    },
    /*
    outputDir:'public',
    pages:{
        index:{

        }
    },
    */
    chainWebpack: config => {

    },
    configureWebpack: {
        plugins: [
        ]
    }
};
