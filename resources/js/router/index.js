import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '@/store';
import {Message} from 'element-ui';

import index from '@/views/default/index';

Vue.use(VueRouter);

/* Layout */
import Layout from '@/layout'

const routes = [
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

const router = new VueRouter({
    routes,
});

router.beforeEach((to, from, next) => {
    let token = JSON.parse(window.localStorage.getItem('userInfo'));
    if (to.path === '/login') {
        if(store.state.user.username){
            Message.warning("您已经登录，请先退出登录!");
            next({path:'/'});
        }else {
            window.localStorage.removeItem('userInfo');
            next();
        }
    } else if (!token && to.path != '/login'){
        next({ path: '/login' });
    } else {
        next();
    }
});

export default router