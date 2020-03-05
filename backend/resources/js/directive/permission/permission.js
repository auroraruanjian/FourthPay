import store from '@/store'

export default {
    inserted(el, binding, vnode) {
        const { value } = binding

        if(typeof value == 'string' &&  value.length > 0){
            if( store.state.user.id == 1 ) return;

            let hasPermission = store.getters.user_permission.includes(value);

            if( !hasPermission ){
                el.parentNode && el.parentNode.removeChild(el)
            }

        }else{
            throw new Error(`need roles! Like v-permission="'admin/index'"`)
        }
    }
}
