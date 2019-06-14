const fetch = window.axios;

export function getRoles(data) {
    return fetch({
        url: 'role',
        method: 'get',
        data
    });
}

export function createRole(data) {
    return fetch({
        url: 'role/create',
        method: 'post',
        data
    });
}

export function getAllPermission(){
    return fetch({
        url: 'role/allPermission',
        method: 'get'
    })
}
