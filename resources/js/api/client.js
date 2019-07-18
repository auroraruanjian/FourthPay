import fetch from '@/utils/fetch'

export function getAllClient( data ) {
    return fetch({
        url: 'client',
        method: 'post',
        params:data
    });
}

export function addClient( data ){
    return fetch({
        url: 'client/create',
        method: 'post',
        data
    });
}

export function getClient( id ){
    return fetch({
        url: 'client/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editClient( data ){
    return fetch({
        url: 'client/edit',
        method: 'put',
        data
    });
}

export function deleteClient( id ){
    return fetch({
        url: 'client/delete',
        method: 'delete',
        params:{id:id},
    });
}