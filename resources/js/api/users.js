import fetch from '@/utils/fetch'

export function getAllUsers( data ) {
    return fetch({
        url: 'users',
        method: 'post',
        params:data
    });
}

export function addUsers( data ){
    return fetch({
        url: 'users/create',
        method: 'post',
        data
    });
}

export function getUsers( id ){
    return fetch({
        url: 'users/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editUsers( data ){
    return fetch({
        url: 'users/edit',
        method: 'put',
        data
    });
}

export function deleteUsers( id ){
    return fetch({
        url: 'users/delete',
        method: 'delete',
        params:{id:id},
    });
}