<template>
    <div class="welcome">
        <i class="el-icon-edit"></i>
        <i class="el-icon-share"></i>
        <i class="el-icon-delete"></i>
        <el-button type="primary" icon="el-icon-search">搜索</el-button>
        <el-button type="primary" icon="el-icon-search" @click="logout">退出</el-button>
        <router-link to="/login">Go to login</router-link>
    </div>
</template>

<script>
    import {logout} from '@/api/user'

    export default {
        date(){

        },
        created(){

        },
        methods:{
            logout(){
                logout().then((response)=>{
                    if( response.data.code == 1 ){
                        this.$message(response.data.msg);
                    }
                });
            }
        }
    }
</script>

<style lang="scss">
</style>