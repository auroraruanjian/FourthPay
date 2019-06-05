import Vue from 'vue';
import VueRouter from 'vue-router';

import index from '@/views/default/index';

Vue.use(VueRouter);

const routes = [
    {
        path: '/',
        component : index,
    },
    {
        path: '/login',
        component : () => import('@/views/users/login'),
    }
]

const router = new VueRouter({
    routes,
});

export default router