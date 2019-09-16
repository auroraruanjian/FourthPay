import fetch from '@/utils/fetch'

export function getAllAdmins( data ) {
    return fetch({
        url: 'admin',
        method: 'get',
        params:data
    });
}

export function addAdmin( data ){
    return fetch({
        url: 'admin/create',
        method: 'post',
        data
    });
}

export function getAdminUser( id ){
    return fetch({
        url: 'admin/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editAdmin( data ){
    return fetch({
        url: 'admin/edit',
        method: 'put',
        data
    });
}

export function deleteAdmin( id ){
    return fetch({
        url: 'admin/delete',
        method: 'delete',
        params:{id:id},
    });
}