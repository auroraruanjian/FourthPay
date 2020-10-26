import fetch from '@/utils/fetch'

export function postPay( data ) {
    return fetch({
        url: 'payment/pay',
        method: 'post',
        data
    });
}
