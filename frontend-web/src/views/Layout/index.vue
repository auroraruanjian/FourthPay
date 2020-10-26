<template>
    <el-container class="app-wrapper" direction="vertical">
        <LayoutHeader></LayoutHeader>
        <el-main><router-view :key="key"></router-view></el-main>
        <LayoutFooter></LayoutFooter>
    </el-container>
</template>

<script>
import header from '@/views/Layout/header'
import footer from '@/views/Layout/footer'


export default {
    name: "index",
    computed: {
        key() {
            return this.$route.path
        }
    },
    components: {
        LayoutHeader:header,
        LayoutFooter:footer
    },
}
</script>

<style >

    .el-aside {
        background-color: #D3DCE6;
        color: #333;
        text-align: center;
    }
    body > .el-container {
        margin-bottom: 40px;
    }
    .container{
        text-align: center;
        margin:0 auto;
        width:1280px;
    }

    .el-header{
        padding: 0 20px;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        -ms-flex-negative: 0;
        flex-shrink: 0;
        position: fixed;
        margin: 0 auto;
        top: 0;
        right: 0;
        left: 0;
        z-index: 1030;
    }
    .el-main {
        /*background-color: #353a4d;*/
        color: #333;
        text-align: center;
        padding:0px;
    }
</style>
