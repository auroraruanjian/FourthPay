const fetch = window.axios;

export function createRole(data) {
    return fetch({
        url: 'role/create',
        method: 'post',
        data
    });
}