import { login,logout } from '@/api/user'

const user = {
    namespaced: true,
    state : {
        token : '',
        avatar: 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
        username: '',
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
                    if( response.data.code == 1 ) {
                        let {data} = response
                        commit('SET_TOKEN', data.data.token)
                        window.localStorage.setItem('userInfo', JSON.stringify({
                            token: data.data.token,
                            username: data.data.username,
                            nickname: data.data.nickname,
                        }));
                    }
                    resolve(response)
                }).catch(error => {
                    reject(error)
                })
            })
        },
        logout({ commit }) {
            return new Promise((resolve, reject) => {
                logout().then( (response) => {
                    commit('SET_TOKEN', '');
                    window.localStorage.removeItem('userInfo');
                    resolve(response)
                }).catch(error => {
                    reject(error)
                })
            })
        }
    }
}

export default user;
