import Vue from 'vue';
import VueRouter from 'vue-router';
import NProgress from 'nprogress' // progress bar
import 'nprogress/nprogress.css' //这个样式必须引入
import store from '@/store';

// import index from '@/views/default/index';

Vue.use(VueRouter);

NProgress.inc(0.2)
NProgress.configure({ easing: 'ease', speed: 500, showSpinner: false })


/* Layout */
import Layout from '@/layout'
import SubPage from '@/views/common/SubPage'

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

/*
export const asyncRoutes = [
    {
        path: '/permission',
        component: Layout,
        redirect: '/permission/index',
        alwaysShow: true, // will always show the root menu
        name: 'Permission',
        meta: {
            title: '权限管理',
            icon: 'lock',
        },
        children:[
            {
                path: '/permission/index',
                name: 'PermissionIndex',
                component: () => import('@/views/permission/index'),
                meta: {
                    title: '权限列表',
                    icon: 'tree-table',
                },
                children : [

                ]
            },
            {
                path: '/role/index',
                name: 'RoleIndex',
                component: () => import('@/views/Role/index'),
                meta: {
                    title: '角色管理',
                    icon: 'peoples',
                },
            }
        ]
    }
]
*/

const router = new VueRouter({
    routes: constantRoutes,
});

router.beforeEach(async (to, from, next) => {
    // start progress bar
    NProgress.start()

    let tokenStore = JSON.parse(window.localStorage.getItem('token'));

    if( tokenStore ){
        if (to.path === '/login') {
            next({path:'/'});
        }else{
            const is_login = store.getters.username && store.getters.username.length > 0

            if( is_login ){
                next()
            }else{
                try {
                    let { permission } = await store.dispatch('user/getUserInfo')

                    // 动态注册路由
                    const accessRoutes = await store.dispatch('permission/generateRoutes', permission)

                    router.addRoutes(accessRoutes)

                    next({...to, replace: true})
                    //next();
                }catch (e) {
                    console.log(e);
                    store.dispatch('user/resetToken').then(()=>{
                        next({path:'/login'});
                    })
                }
            }
        }

    }else{
        if( to.path === '/login' ){
            next();
        }else{
            next({path:'/login'});
        }
    }
});

router.afterEach(() => {
    NProgress.done()
})

export default router