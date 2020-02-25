import fetch from '@/utils/fetch'

export function getAllCategory() {
    return fetch({
        url: 'payment_category',
        method: 'get',
    });
}

export function createCategory(data) {
    return fetch({
        url: 'payment_category/create',
        method: 'post',
        data
    });
}

export function getCategory(id) {
    return fetch({
        url: 'payment_category/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editCategory( data )
{
    return fetch({
        url: 'payment_category/edit',
        method: 'put',
        data
    });
}

export function deleteCategory( id ){
    return fetch({
        url: 'payment_category/delete',
        method: 'delete',
        params:{id:id},
    });
}
