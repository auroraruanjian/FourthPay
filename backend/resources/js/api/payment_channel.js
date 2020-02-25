import fetch from '@/utils/fetch'

export function getAllChannel() {
    return fetch({
        url: 'payment_channel',
        method: 'get',
    });
}

export function createChannel(data) {
    return fetch({
        url: 'payment_channel/create',
        method: 'post',
        data
    });
}

export function getChannel(id) {
    return fetch({
        url: 'payment_channel/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editChannel( data )
{
    return fetch({
        url: 'payment_channel/edit',
        method: 'put',
        data
    });
}

export function deleteChannel( id ){
    return fetch({
        url: 'payment_channel/delete',
        method: 'delete',
        params:{id:id},
    });
}
