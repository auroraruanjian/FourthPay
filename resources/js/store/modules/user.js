import { login } from '@/api/user'

const user = {
    namespaced: true,
    state : {
        token : '',
    },
    mutations: {
        SET_TOKEN : ( state ,token )=>{
            state.token = token;
        }
    },
    actions: {
        login({ commit }, userInfo) {
            const { username, password } = userInfo
            return new Promise((resolve, reject) => {
                login({ username: username.trim(), password: password }).then(response => {
                    let { data } = response
                    commit('SET_TOKEN', data.data.token)
                    window.localStorage.setItem('userInfo',JSON.stringify({
                        token:data.data.token,
                        username:data.data.username,
                        nickname:data.data.nickname,
                    }));
                    resolve(response)
                }).catch(error => {
                    reject(error)
                })
            })
        },
    }
}

export default user;
