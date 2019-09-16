<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" icon="el-icon-circle-plus-outline" v-permission="'notices/create'" @click="$router.push({path:'/notices/create'})" size="small">新建公告</el-button>
            </div>

            <el-table :data="noticesList" style="width: 100%;margin-top:30px;" border>
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="标题" prop="subject"></el-table-column>
                <el-table-column align="header-center" label="创建管理员" prop="created_admin_username"></el-table-column>
                <el-table-column align="header-center" label="审核管理员" prop="verified_admin_username"></el-table-column>
                <el-table-column align="header-center" label="标记">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.is_top">置顶</el-tag>
                        <el-tag v-if="scope.row.is_alert" type="success">弹出</el-tag>
                        <el-tag v-if="scope.row.is_show" type="warning">显示</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="创建时间" prop="created_at"></el-table-column>
                <el-table-column align="header-center" label="发布时间" prop="published_at"></el-table-column>
                <el-table-column align="header-center" label="审核时间" prop="verified_at"></el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope">
                        <el-button type="primary" size="small" @click="$router.push({path:`/notices/edit/${scope.row.id}`})" v-permission="'notices/edit'">Edit</el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope)" v-permission="'notices/delete'">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getNotices" />
        </div>
    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js' // 权限判断指令
    import { getNotices } from '@/api/notice'
    import Pagination from '@/components/Pagination' // Secondary package based on el-pagination

    const defaultPermission = {
        id:'',
        subject:'',
        is_show:'',
        is_top:'',
        is_alert:'',
        content:'',
    };

    export default {
        name: "notice_index",
        directives: { permission },
        data(){
            return {
                notices: Object.assign({}, defaultPermission),
                noticesList: [],
                loading:false,
                total: 0,
                listQuery: {
                    page: 1,
                    limit: 20
                },
            };
        },
        components: { Pagination },
        computed: {},
        methods:{
            async getNotices(){
                this.loading =  false;
                let result = await getNotices(this.listQuery);
                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.noticesList = result.data.data.noticelist;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAdd(){

            },
            handleEdit( scope ){

            },
            handleDelete( scope ){

            }
        },
        created() {
            this.getNotices();
        }
    }
</script>

<style scoped>

</style>