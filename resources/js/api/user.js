import fetch from '@/utils/fetch'

export function login(data) {
    return fetch({
        url: 'login',
        method: 'post',
        data
    });
}

export function logout() {
    return fetch({
        url: 'logout',
        method: 'post',
    });
}

export function getUserInfo(){
    return fetch({
        url: 'admin/info',
        method: 'get',
    });
}

export function wechat_login( state = '' , mode = 'web') {
    return fetch({
        url: 'login/wechat',
        method: 'get',
        params :{state,mode}
    });
}

export function unbind_wechat() {
    return fetch({
        url: 'admin/unbindWechat',
        method: 'put',
    });
}