const path = require('path');

module.exports = {
    productionSourceMap:false,
    devServer:{
        proxy: {
            '/api': {
                target: process.env.VUE_APP_DOMAIN,
                ws: true,
                changeOrigin: true,
                pathRewrite: {'^/api' : ''}
            },
        }
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
