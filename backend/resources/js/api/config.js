import fetch from '@/utils/fetch'

export function getAllConfig( data ) {
    return fetch({
        url: 'config',
        method: 'post',
        params:data
    });
}

export function addConfig( data ){
    return fetch({
        url: 'config/create',
        method: 'post',
        data
    });
}

export function getConfig( id ){
    return fetch({
        url: 'config/edit',
        method: 'get',
        params:{id:id}
    });
}

export function settingConfig( data ){
    return fetch({
        url: 'config/setting',
        method: 'put',
        data
    });
}

export function editConfig( data ){
    return fetch({
        url: 'config/edit',
        method: 'put',
        data
    });
}

export function deleteConfig( id ){
    return fetch({
        url: 'config/delete',
        method: 'delete',
        params:{id:id},
    });
}