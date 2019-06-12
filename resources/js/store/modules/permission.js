import { constantRoutes } from '@/router'
import { createRouter } from '@/utils/';


const state = {
    routes: [],
    addRoutes: []
}

const mutations = {
    SET_ROUTES: (state, routes) => {
        state.addRoutes = routes
        state.routes = constantRoutes.concat(routes)
    }
}

const actions = {
    generateRoutes({ commit }, permission) {
        return new Promise(resolve => {
            let asyncRoutes = createRouter(permission);
            console.log(asyncRoutes);
            commit('SET_ROUTES', asyncRoutes);
            resolve(asyncRoutes)
        })
        // return new Promise(resolve => {
        //     //console.log(permission);
        //     let asyncRoutes = this.createRouter(permission);
        //     console.log(asyncRoutes);
        //     commit('SET_ROUTES', asyncRoutes);
        //     resolve(asyncRoutes)
        // })
        // */
    }
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
