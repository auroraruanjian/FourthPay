import fetch from '@/utils/fetch'

export function getAllPermissions(id) {
    return fetch({
        url: 'permission',
        method: 'get',
        params:{id:id}
    });
}

export function addPermission( data ){
    return fetch({
        url: 'permission/create',
        method: 'post',
        data
    });
}

export function getPermission( id ){
    return fetch({
        url: 'permission/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editPermission( data ){
    return fetch({
        url: 'permission/edit',
        method: 'put',
        data
    });
}

export function deletePermission( id ){
    return fetch({
        url: 'permission/delete',
        method: 'delete',
        params:{id:id},
    });
}