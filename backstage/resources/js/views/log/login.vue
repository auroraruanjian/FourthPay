<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-autocomplete
                        class="inline-input"
                        v-model="search.domain"
                        :fetch-suggestions="querySearch"
                        placeholder="访问域名"
                        size="small"
                        @select="handleSelect">
                </el-autocomplete>
                <el-button type="primary" icon="el-icon-search" @click="handleSearch" size="small">搜索</el-button>
            </div>

            <el-table :data="login_log" style="width: 100%;margin-top:30px;" border>
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="center" label="用户名" prop="username"></el-table-column>
                <el-table-column align="header-center" label="访问域名" prop="domain"></el-table-column>
                <el-table-column align="header-center" label="来源" prop="province"></el-table-column>
                <el-table-column align="header-center" label="浏览器" prop="browser"></el-table-column>
                <el-table-column align="header-center" label="浏览器版本" prop="browser_version"></el-table-column>
                <el-table-column align="header-center" label="操作系统" prop="os"></el-table-column>
                <el-table-column align="header-center" label="设备类型" prop="device"></el-table-column>
                <el-table-column align="header-center" label="IP" prop="ip"></el-table-column>
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
    import { getLoginLogs } from '@/api/log'


    const defaultLog = {
        id : '',
        username : '',
        domain : '',
        province : '',
        browser : '',
        browser_version : '',
        os : '',
        device : '',
        ip : '',
        created_at : [],
    };

    export default{
        data(){
            return {
                log: Object.assign({}, defaultLog),
                login_log: [],
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
                total: 0,
                listQuery: {
                    page: 1,
                    limit: 20
                },
                search:{
                    domain:'',
                },
                allDomain:[],
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
                let result = await getLoginLogs(this.listQuery);
                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.login_log = result.data.data.log;
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
                var allDomain = this.allDomain;
                var results = queryString ? allDomain.filter(this.createFilter(queryString)) : allDomain;
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