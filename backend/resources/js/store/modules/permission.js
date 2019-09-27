import { constantRoutes } from '@/router'
import { createRouter } from '@/utils/';


const state = {
    routes: [],
    addRoutes: [],
    permission:[],
}

const mutations = {
    SET_ROUTES: (state, routes) => {
        state.addRoutes = routes
        state.routes = constantRoutes.concat(routes)
    },
    SET_PERMISSION: (state, permission) => {
        state.permission = permission;
    }
}

const actions = {
    generateRoutes({ commit }, apiRouters) {
        return new Promise(resolve => {
            let routers = createRouter(apiRouters);
            // console.log(routers.asyncRouter,routers.user_permission);
            commit('SET_ROUTES', routers.asyncRouter);
            commit('SET_PERMISSION', routers.user_permission);
            resolve(routers.asyncRouter)
        })
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
