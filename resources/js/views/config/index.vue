<template>
    <div class="app-container" v-loading="loading">
        <el-button type="primary" @click="handleAddConfig" v-permission="'config/create'">创建配置</el-button>

        <el-table :data="config_list" style="width: 100%;margin-top:30px;" border>
            <el-table-column align="center" label="ID" prop="id"></el-table-column>
            <el-table-column align="center" label="用户名" prop="title"></el-table-column>
            <el-table-column align="header-center" label="昵称" prop="key"></el-table-column>
            <el-table-column align="header-center" label="昵称" prop="value"></el-table-column>
            <el-table-column align="header-center" label="状态" >
                <template slot-scope="scope">
                    <el-tag type="success" v-if="scope.row.is_disabled">正常</el-tag>
                    <el-tag type="danger" v-else>禁用</el-tag>
                </template>
            </el-table-column>
            <el-table-column align="center" label="Operations">
                <template slot-scope="scope" >
                    <el-button type="primary" size="small" @click="handleEdit(scope)">Edit</el-button>
                    <el-button type="danger" size="small" @click="handleDelete(scope)">Delete</el-button>
                </template>
            </el-table-column>
        </el-table>

        <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getAllConfig" />

    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js' // 权限判断指令
    import Pagination from '@/components/Pagination' // Secondary package based on el-pagination


    const defaultConfig = {
        id:'',
        parent_id:'',
        title:'',
        key:'',
        value:'',
        description:'',
        is_disabled:'',
    };

    export default {
        name: "config",
        data(){
            return {
                config: Object.assign({}, defaultConfig),
                config_list: [],
                loading:false,
                total: 0,
                listQuery: {
                    page: 1,
                    limit: 20
                }
            };
        },
        computed: {

        },
        components: { Pagination },
        directives: { permission },
        created() {
            this.getAllConfig();
        },
        methods:{
            getAllConfig(){

            },
            handleAddConfig(){

            },
        }
    }
</script>

<style scoped>

</style>