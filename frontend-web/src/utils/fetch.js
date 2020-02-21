import { Message } from 'element-ui'
import axios from 'axios'
import store from '@/store'
import router from '@/router'

// create an axios instance
const fetch = axios.create({
    baseURL: 'http://api.laravel_admin.me',//'/api/', // url = base url + request url
    // withCredentials: true, // send cookies when cross-domain requests
    timeout: 5000 // request timeout
})

fetch.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

fetch.interceptors.response.use(
    response => {
        return response;
    },
    error => {
        console.log(error);
        if( error.response ) {
            if( error.response.status == 419 || error.response.status == 401) {
                Message({
                    message: error.response.data.msg || 'Error',
                    type: 'error',
                    duration: 5 * 1000
                });
                store.dispatch('user/resetToken').then(() => {
                    if(error.response.status == 419){
                        location.reload()
                    }else{
                        router.push({path:'login'});
                    }
                })
            }else if( error.response.status == 422 ){
                for(let i in error.response.data.data.errors){
                    let err_msg = '';
                    for( let e in error.response.data.data.errors[i] ){
                        err_msg += error.response.data.data.errors[i][e];
                    }
                    error.response.data.msg = err_msg;
                    return error.response;
                }
            }
            return Promise.reject(new Error(error.message || 'Error'))
        } else {
            return Promise.reject(new Error(error.message || 'Error'))
        }
    }
);

export default fetch
