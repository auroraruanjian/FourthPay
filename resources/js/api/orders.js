import fetch from '@/utils/fetch'

export function getOrders( data ) {
    return fetch({
        url: 'orders',
        method: 'get',
        params:data
    });
}
