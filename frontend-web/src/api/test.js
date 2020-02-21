import fetch from '@/utils/fetch'

export function getTest() {
    return fetch({
        url: 'test',
        method: 'get',
    });
}
