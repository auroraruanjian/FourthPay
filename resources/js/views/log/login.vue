<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-autocomplete
                        class="inline-input"
                        v-model="search.path"
                        :fetch-suggestions="querySearch"
                        placeholder="请输入需要查找的访问路径"
                        size="small"
                        @select="handleSelect">
                </el-autocomplete>
                <el-button type="primary" icon="el-icon-search" @click="handleSearch" size="small">搜索</el-button>
            </div>

            <el-table :data="request_log" style="width: 100%;margin-top:30px;" border>
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="center" label="用户名" prop="username"></el-table-column>
                <el-table-column align="header-center" label="访问路径" prop="path"></el-table-column>
                <el-table-column align="header-center" label="请求数据" prop="request"></el-table-column>
                <el-table-column align="header-center" label="时间" prop="created_at"></el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope">
                        <el-button type="primary" size="small" @click="handleView(scope)">详情</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getLogs" />
        </div>
    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js' // 权限判断指令
    import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
    import { getRequestLogs } from '@/api/log'


    const defaultLog = {
        id : '',
        username : '',
        path : '',
        request : '',
        created_at : '',
    };

    export default{
        data(){
            return {
                log: Object.assign({}, defaultLog),
                request_log: [],
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
                total: 0,
                listQuery: {
                    page: 1,
                    limit: 20
                },
                search:{
                    path:'',
                },
                allPath:[],
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
                let result = await getRequestLogs(this.listQuery);
                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.request_log = result.data.data.log;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleView( scope ){

            },
            createFilter(queryString) {
                return (restaurant) => {
                    return (restaurant.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },
            querySearch(queryString, cb) {
                var allPath = this.allPath;
                var results = queryString ? allPath.filter(this.createFilter(queryString)) : allPath;
                // 调用 callback 返回建议列表的数据
                cb(results);
            },
            handleSelect( item ){
                console.log(item);
            },
            handleSearch(){

            }
        }
    }
</script>