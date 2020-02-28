import fetch from '@/utils/fetch'

export function getAllMerchant( data ) {
    return fetch({
        url: 'merchant',
        method: 'post',
        params:data
    });
}

export function addMerchant( data ){
    return fetch({
        url: 'merchant/create',
        method: 'post',
        data
    });
}

export function getMerchant( id ){
    return fetch({
        url: 'merchant/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editMerchant( data ){
    return fetch({
        url: 'merchant/edit',
        method: 'put',
        data
    });
}

export function deleteMerchant( id ){
    return fetch({
        url: 'merchant/delete',
        method: 'delete',
        params:{id:id},
    });
}
