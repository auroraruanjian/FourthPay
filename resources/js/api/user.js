const fetch = window.axios;

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