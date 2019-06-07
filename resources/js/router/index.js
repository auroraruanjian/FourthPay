import Vue from 'vue';
import VueRouter from 'vue-router';
import NProgress from 'nprogress' // progress bar
import store from '@/store';
import {Message} from 'element-ui';

// import index from '@/views/default/index';

Vue.use(VueRouter);
NProgress.configure({ showSpinner: false }) // NProgress Configuration

/* Layout */
import Layout from '@/layout'

export const constantRoutes = [
    {
        path: '/login',
        component : () => import('@/views/users/login'),
    },

    {
        path: '/',
        component : Layout,
        redirect: '/dashboard',
        children: [
            {
                path: '/dashboard',
                name: 'Dashboard',
                component: () => import('@/views/dashboard/index'),
                meta:{ title: 'Dashboard', icon: 'dashboard' },
            }
        ]
    },
]

export const asyncRoutes = [

]

const router = new VueRouter({
    routes: constantRoutes,
});

router.beforeEach((to, from, next) => {
    // start progress bar
    NProgress.start()

    let token = JSON.parse(window.localStorage.getItem('userInfo'));
    if( token ){
        if (to.path === '/login') {
            next({path:'/'});
            NProgress.done();
        }else{
            // 权限检查注册
            next()
        }
    }else{
        next(`/login?redirect=${to.path}`)
        NProgress.done()
    }


    NProgress.done()
});

export default router