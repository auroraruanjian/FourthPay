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
        url: 'user/info',
        method: 'get',
    });
}