import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getters'
import app from './modules/app'
import user from './modules/user'
import settings from './modules/user'

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        app,
        user,
        settings
    },
    getters
})

export default store
