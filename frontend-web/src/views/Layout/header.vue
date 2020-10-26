<template>
    <el-header :class="header_model" height="auto">
        <div class="container">
            <img src="@/assets/logo.png" style="float:left;margin:16px;width: 200px;">
            <el-menu
                :default-active="activeIndex"
                mode="horizontal"
                active-text-color="#409EFF"
                style="display: inline-block;float: left;border-bottom: 0px;background-color: transparent;"
                @select="handleSelect">
                <el-menu-item index="1">首页</el-menu-item>
                <el-submenu index="2" :show-timeout="0" :hide-timeout="0">
                    <template slot="title">产品</template>
                    <el-menu-item index="2-1">个人H5支付结算系统</el-menu-item>
                    <el-menu-item index="2-2">跨境支付清算系统</el-menu-item>
                    <el-menu-item index="2-3">电商支付分账系统</el-menu-item>
                    <!--
                    <el-submenu index="2-4">
                        <template slot="title">选项4</template>
                        <el-menu-item index="2-4-1">选项1</el-menu-item>
                        <el-menu-item index="2-4-2">选项2</el-menu-item>
                        <el-menu-item index="2-4-3">选项3</el-menu-item>
                    </el-submenu>
                    -->
                </el-submenu>
                <el-menu-item index="3" >案例</el-menu-item>
                <el-submenu index="4" :show-timeout="0" :hide-timeout="0">
                    <template slot="title">合作</template>
                    <el-menu-item index="4-1">服务商合作</el-menu-item>
                    <el-menu-item index="4-2">商户合作</el-menu-item>
                    <el-menu-item index="4-3">代理商合作</el-menu-item>
                </el-submenu>
                <el-menu-item index="5">博客</el-menu-item>
                <el-menu-item index="6">关于我们</el-menu-item>
            </el-menu>
            <el-button type="primary" style="margin:16px;float: right;" round>服务商合作</el-button>
        </div>
    </el-header>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
    name: "layout_header",
    data(){
        return {
            activeIndex: '1',
        };
    },
    computed: {
        ...mapGetters([
            'header_model',
        ]),
    },
    methods: {
        handleSelect(key, keyPath) {
            console.log(key, keyPath);
        }
    }
}
</script>

<style scoped lang="scss" >
.light{
    background-color: #fff;
    box-shadow: 0px 0px 18px 1px rgba(0, 0, 0, 0.1);
    transition: all 0.5s ease-in-out;

    /deep/ .el-menu--horizontal{
        .el-submenu {
            color: #666;
            .el-submenu__title,& i{
                color: #666;
            }
        }
        .el-menu-item{
            color: #666;
        }
    }
}
/deep/ .el-menu--horizontal{
    padding: .5rem 1rem .5rem 5rem;

    .el-submenu {
        color: #fff;
        .el-submenu__title,& i{
            color: #fff;
            font-size: 16px;
        }
    }
    .el-menu-item{
        color: #fff;
        font-size: 16px;
    }
    .el-menu-item:not(.is-disabled):focus, .el-menu-item:not(.is-disabled):hover, .el-submenu .el-submenu__title:hover {
        background-color: transparent;
        color:#409EFF;
        i{
            color:#409EFF;
        }
    }
}
</style>
