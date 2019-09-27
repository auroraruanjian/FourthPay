<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-breadcrumb separator-class="el-icon-arrow-right">
                    <el-breadcrumb-item><el-link @click.native="path=''" :underline="false">主目录</el-link></el-breadcrumb-item>
                    <el-breadcrumb-item v-for="(item,key) in tree" :key="key"><el-link @click.native="path=item.path" :underline="false">{{item.name}}</el-link></el-breadcrumb-item>
                </el-breadcrumb>
            </div>

            <el-table :data="list" style="width: 100%;margin-top:30px;" border>
                <el-table-column align="center" label="名称">
                    <template slot-scope="scope">
                        <el-link v-if="scope.row.type=='dir'" icon="el-icon-folder-opened" @click.native="path=scope.row.name">{{scope.row.name}}</el-link>
                        <el-link v-else icon="el-icon-document" :underline="false">{{scope.row.name}}</el-link>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="文件大小" prop="size"></el-table-column>
                <el-table-column align="center" label="最后修改时间" prop="lastModified"></el-table-column>

                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope" v-if="scope.row.type=='file'">
                        <el-button type="primary" size="small" @click="open(scope.row,'view')" v-if="scope.row.size<5">查看</el-button>
                        <el-button type="primary" size="small" @click="open(scope.row)">下载</el-button>
                    </template>
                </el-table-column>
            </el-table>

        </div>
    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js' // 权限判断指令
    import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
    import { getSystemLogs } from '@/api/log'

    export default{
        data(){
            return {
                list: [],
                tree: [],
                loading:false,
                path:'',
            };
        },
        components: { Pagination },
        computed: {
        },
        directives: { permission },
        created() {
            this.getLogs();
        },
        methods: {
            async getLogs(){
                this.loading =  true;
                let result = await getSystemLogs(this.path);
                if( result.data.code == 1 ){
                    this.list = result.data.data.list;
                    this.tree = result.data.data.tree;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            open(row,type = 'download'){
                let url = '//'+location.host+'/log/systemLogFile?filename='+row.name
                if( type == 'view' ){
                    url += '&flag=view'
                }
                window.open(url);
            }
        },
        watch:{
            path:function(){
                this.getLogs();
            }
        }
    }
</script>