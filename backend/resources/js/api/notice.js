import fetch from '@/utils/fetch'

export function getNotices( data ) {
    return fetch({
        url: 'notices/index',
        method: 'post',
        data
    });
}

export function postCreate( data ) {
    return fetch({
        url: 'notices/create',
        method: 'post',
        data
    });
}

export function getEdit( id ) {
    return fetch({
        url: 'notices/edit',
        method: 'get',
        params:{id}
    });
}

export function putEdit( data ) {
    return fetch({
        url: 'notices/edit',
        method: 'put',
        data
    });
}