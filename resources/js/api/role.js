const fetch = window.axios;

export function getAllRoles() {
    return fetch({
        url: 'role',
        method: 'get',
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

export function getRole(id) {
    return fetch({
        url: 'role/edit',
        method: 'get',
        params:{id:id}
    });
}
