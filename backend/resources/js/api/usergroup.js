import fetch from '@/utils/fetch'

export function getAllUserGroups( data ) {
    return fetch({
        url: 'user_group',
        method: 'post',
        params:data
    });
}

export function addUserGroup( data ){
    return fetch({
        url: 'user_group/create',
        method: 'post',
        data
    });
}

export function getUserGroup( id ){
    return fetch({
        url: 'user_group/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editUserGroup( data ){
    return fetch({
        url: 'user_group/edit',
        method: 'put',
        data
    });
}

export function deleteUserGroup( id ){
    return fetch({
        url: 'user_group/delete',
        method: 'delete',
        params:{id:id},
    });
}
