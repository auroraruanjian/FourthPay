// import * as bootstrap from './bootstrap'; // 为了保证顺序执行，bootstrap 和 config 必须使用 import from 语法

import Vue from 'vue'

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import App from '@/views/app.vue';
import router from '@/router';
import store from '@/store';
import VueCookie from 'vue-cookie' ;

import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';

import vueParticleLine from 'vue-particle-line'
import 'vue-particle-line/dist/vue-particle-line.css'
Vue.use(vueParticleLine)

import '../sass/index.scss' // global css

import '@/icons' // icon

window.Vue = require('vue');

Vue.use(ElementUI);
Vue.use(VueCookie);

require('./bootstrap');
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */



new Vue({
    el: '#app',
    router,
    store,
    render: h => h(App),
});

