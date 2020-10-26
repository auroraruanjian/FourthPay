
const state = {
    header_model: 'light'
}

const mutations = {
    TOGGLE_HEADER_MODEL: (state, model) => {
        state.header_model = model
    }
}

const actions = {
    toggleHeaderModel({ commit }, model) {
        commit('TOGGLE_HEADER_MODEL',model)
    },
}

export default {
    namespaced: true,
    state,
    mutations,
    actions
}
