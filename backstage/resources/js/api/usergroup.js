import fetch from '@/utils/fetch'

export function getAllUserGroups( data ) {
    return fetch({
        url: 'usergroup',
        method: 'post',
        params:data
    });
}

export function addUserGroup( data ){
    return fetch({
        url: 'usergroup/create',
        method: 'post',
        data
    });
}

export function getUserGroup( id ){
    return fetch({
        url: 'usergroup/edit',
        method: 'get',
        params:{id:id}
    });
}

export function editUserGroup( data ){
    return fetch({
        url: 'usergroup/edit',
        method: 'put',
        data
    });
}

export function deleteUserGroup( id ){
    return fetch({
        url: 'usergroup/delete',
        method: 'delete',
        params:{id:id},
    });
}