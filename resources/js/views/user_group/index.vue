<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAdd" v-permission="'user_group/create'" size="small">创建用户组</el-button>
            </div>

            <el-table :data="user_group_list" style="width: 100%;margin-top:30px;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="用户组名称" prop="name"></el-table-column>
                <el-table-column align="header-center" label="创建时间" prop="created_at"></el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope" >
                        <el-button type="primary" size="small" @click="handleEdit(scope)">Edit</el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getAllUserGroups" />
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'编辑用户组':'新建用户组'">
            <el-form :model="user_group" label-width="15%" label-position="right">
                <el-form-item label="用户组名称">
                    <el-input v-model="user_group.name" placeholder="用户组名称" />
                </el-form-item>
                <el-form-item label="是否启用">
                    <el-switch
                            v-model="user_group.status"
                            active-color="#13ce66"
                            inactive-color="#ddd">
                    </el-switch>
                </el-form-item>
            </el-form>
            <div style="text-align:right;">
                <el-button type="danger" @click="dialogVisible=false">Cancel</el-button>
                <el-button type="primary" @click="confirm">Confirm</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js' // 权限判断指令
    import Pagination from '@/components/Pagination' // Secondary package based on el-pagination
    import { getAllUserGroups,getUserGroup,editUserGroup,addUserGroup } from '@/api/usergroup'
    import { mapGetters } from 'vuex'


    const defaultUserGroup = {
        id: '',
        name: '',
        status: true,
    };

    export default {
        name: "UserGroupIndex",
        data(){
            return {
                user_group: Object.assign({}, defaultUserGroup),
                user_group_list:[],
                total: 0,
                listQuery: {
                    page: 1,
                    limit: 20
                },
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
            };
        },
        computed: {
            ...mapGetters([
                'username'
            ])
        },
        components: { Pagination },
        directives: { permission },
        created() {
            this.getAllUserGroups();
        },
        methods:{
            async getAllUserGroups(){
                this.loading =  true;

                let data = this.listQuery;
                let result = await getAllUserGroups(data);

                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.user_group_list = result.data.data.user_group;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAdd(){
                this.user_group = Object.assign({}, defaultUserGroup)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                this.loading =  true;
                let current_users = await getUserGroup(scope.row.id);;//
                this.user_group = current_users.data.data;
                this.user_group.status = (this.user_group.status==1)?true:false;
                this.dialogType = 'edit'
                this.dialogVisible = true
                this.loading =  false;
            },
            handleDelete( scope ){
                this.$confirm('此操作将永久删除该用户组, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then( async() => {
                    let result = await deleteUserGroup( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAllUsers();
                    }else{
                        this.$message.error(result.data.message);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            async confirm(){
                const isEdit = this.dialogType === 'edit'

                let type = 'error';
                let message = '';

                let response;

                if (isEdit) {
                    response = await editUserGroup(this.user_group)
                }else{
                    response = await addUserGroup(this.user_group)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>用户组: ${this.user_group.name}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllUserGroups();

                this.$notify({
                    title: response.data.msg,
                    dangerouslyUseHTMLString: true,
                    message: message,
                    type: type
                })
            },
        },
        watch: {

        }
    }
</script>

<style scoped>

</style>