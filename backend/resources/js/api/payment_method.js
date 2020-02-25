import fetch from '@/utils/fetch'

export function getAllMethod() {
    return fetch({
        url: 'payment_method',
        method: 'get',
    });
}

export function createMethod(data) {
    return fetch({
        url: 'payment_method/create',
        method: 'post',
        data
    });
}

export function getMethod(id) {
    return fetch({
        url: 'payment_method/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editMethod( data )
{
    return fetch({
        url: 'payment_method/edit',
        method: 'put',
        data
    });
}

export function deleteMethod( id ){
    return fetch({
        url: 'payment_method/delete',
        method: 'delete',
        params:{id:id},
    });
}
