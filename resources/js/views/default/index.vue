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
    export default {
        date(){

        },
        created(){

        },
        methods:{
            logout() {
                this.$store.dispatch('user/logout').then((response) => {
                    if (response.data.code == 1) {
                        this.$message(response.data.msg);
                    }
                    this.$router.push({path:'/login'});
                }).catch((e) => {
                    console.log(e);
                });
            }
        },
        mounted() {


        }
    }
</script>

<style lang="scss">
    #canvascontainer{
        position: absolute;
        top: 0px;
    }
</style>