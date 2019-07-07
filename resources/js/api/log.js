import fetch from '@/utils/fetch'

export function getRequestLogs( data ) {
    return fetch({
        url: 'log/requestLog',
        method: 'get',
        params:data
    });
}

export function getLoginLogs( data ) {
    return fetch({
        url: 'log/loginLog',
        method: 'get',
        params:data
    });
}