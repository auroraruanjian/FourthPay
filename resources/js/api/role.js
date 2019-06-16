import fetch from '@/utils/fetch'

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

export function getRole(id) {
    return fetch({
        url: 'role/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editRole( data )
{
    return fetch({
        url: 'role/edit',
        method: 'put',
        data
    });
}
