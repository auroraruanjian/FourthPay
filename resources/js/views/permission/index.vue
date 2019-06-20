<template>
    <div class="app-container" v-loading="loading">
        <el-button type="primary" @click="handleAdd" v-permission="'permission/create'">创建权限</el-button>

        <el-table :data="permissionList" style="width: 100%;margin-top:30px;" border>
            <el-table-column align="center" label="ID" prop="id"></el-table-column>
            <el-table-column align="center" label="规则">
                <template slot-scope="scope">
                    <el-link type="primary" @click="getAllPermission(scope.row.id)">{{scope.row.rule}}</el-link>
                </template>
            </el-table-column>
            <el-table-column align="header-center" label="名称" prop="name"></el-table-column>
            <el-table-column align="center" label="Operations">
                <template slot-scope="scope">
                    <el-button type="primary" size="small" @click="handleEdit(scope)" v-permission="'permission/edit'">Edit</el-button>
                    <el-button type="danger" size="small" @click="handleDelete(scope)" v-permission="'permission/delete'">Delete</el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js' // 权限判断指令
    import { getAllPermissions } from '@/api/permission'

    const defaultPermission = {
        id:'',
        parent_id:'',
        rule:'',
        name:'',
        extra:[],
        description:'',
    };

    export default{
        data(){
            return {
                permission: Object.assign({}, defaultPermission),
                permissionList: [],
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
            };
        },
        computed: {
        },
        directives: { permission },
        created() {
            this.getAllPermission(0);
        },
        methods:{
            async getAllPermission(id){
                this.loading =  true;
                let result = await getAllPermissions(id);
                if( result.data.code == 1 ){
                    this.permissionList = result.data.data;
                }else{
                    this.$message.error(result.data.msg);
                }
                this.loading =  false;
            },
            handleAdd(){
                this.admin = Object.assign({}, defaultAdmin)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            handleEdit( scope ){

            },
            handleDelete( scope ){

            }
        }
    }
</script>