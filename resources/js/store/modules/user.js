import { login,logout,getUserInfo } from '@/api/user'

const user = {
    namespaced: true,
    state : {
        token : '',
        avatar: 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
        username: '',
        nickname: '',
        wechat_status : false,
    },
    mutations: {
        SET_TOKEN : (state , token) => {
            state.token = token;
            if(typeof token == 'undefined' || token == null || token == '' ){
                window.localStorage.removeItem('token');
            }else{
                window.localStorage.setItem('token', JSON.stringify({
                    token: token,
                }));
            }
        },
        SET_USERINFO : ( state , data ) => {
            state.username = data.username;
            state.nickname = data.nickname;
        },
        SET_WECHAT_STATUS:( state , wechat_status ) => {
            state.wechat_status = wechat_status;
        },
    },
    actions: {
        login({ commit }, userInfo) {
            const { username, password } = userInfo
            return new Promise((resolve, reject) => {
                login({ username: username.trim(), password: password }).then(response => {
                    if( response.data.code == 1 ) {
                        commit('SET_TOKEN',response.data.data.token)
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
                    this.dispatch('user/resetToken');
                    resolve(response)
                }).catch(error => {
                    reject(error)
                })
            })
        },
        getUserInfo({commit}){
            return new Promise((resolve, reject) => {
                getUserInfo().then( (response) => {
                    if( response.data.code == 1 ){
                        commit('SET_USERINFO', response.data.data);
                        commit('SET_WECHAT_STATUS', response.data.data.wechat_status);
                    }

                    resolve(response.data.data)
                }).catch(error => {
                    reject(error)
                })
            })
        },
        resetToken({commit}){
            return new Promise(resolve => {
                commit('SET_TOKEN', '');
                commit('SET_USERINFO', []);
                resolve()
            })
        }
    }
}

export default user;
