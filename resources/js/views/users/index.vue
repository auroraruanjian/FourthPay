<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAddUsers" v-permission="'users/create'" size="small">创建用户</el-button>
            </div>

            <el-table :data="users_list" style="width: 100%;margin-top:30px;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="商户名称" prop="account"></el-table-column>
                <el-table-column align="header-center" label="用户名" prop="username"></el-table-column>
                <el-table-column align="header-center" label="用户组" prop="user_group_name"></el-table-column>
                <el-table-column align="header-center" label="昵称" prop="nickname"></el-table-column>
                <el-table-column align="header-center" label="上次登录IP" prop="last_ip"></el-table-column>
                <el-table-column align="header-center" label="上次登录时间" prop="last_time"></el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope" >
                        <el-button type="primary" size="small" @click="handleEdit(scope)">Edit</el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getAllUsers" />
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'Edit Users':'New Users'">
            <el-form :model="users" label-width="15%" label-position="right">
                <el-form-item label="商户">
                    <el-input v-model="users.client_id" placeholder="商户" />
                </el-form-item>
                <el-form-item label="用户名">
                    <el-input v-model="users.username" placeholder="用户名" />
                </el-form-item>
                <el-form-item label="昵称">
                    <el-input v-model="users.nickname" placeholder="昵称" />
                </el-form-item>
                <el-form-item label="密码">
                    <el-input v-model="users.password" placeholder="密码" type="password" />
                </el-form-item>
                <el-form-item label="是否启用">
                    <el-switch
                            v-model="users.status"
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
    import { getAllUsers,editUsers,addUsers,getUsers,deleteUsers } from '@/api/users'
    import { mapGetters } from 'vuex'


    const defaultUsers = {
        id:'',
        client_id:'1',
        username: '',
        user_group_id: '1',
        nickname: '',
        password:'',
        status:true,
    };

    export default {
        name: "UsersIndex",
        data(){
            return {
                users: Object.assign({}, defaultUsers),
                users_list: [],
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
            this.getAllUsers();
        },
        methods:{
            async getAllUsers(){
                this.loading =  true;

                let data = this.listQuery;
                data.parent_id = this.parent_id;
                let result = await getAllUsers(data);

                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.users_list = result.data.data.users_list;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAddUsers(){
                this.users = Object.assign({}, defaultUsers)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                this.loading =  true;
                let current_users = await getUsers(scope.row.id);
                this.users = current_users.data.data;
                this.dialogType = 'edit'
                this.dialogVisible = true
                this.loading =  false;
            },
            handleDelete( scope ){
                this.$confirm('此操作将永久删除该配置, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then( async() => {
                    let result = await deleteUsers( scope.row.id )
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
                    response = await editUsers(this.users)
                }else{
                    response = await addUsers(this.users)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>Users name: ${this.users.title}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllUsers();

                this.$notify({
                    title: response.data.msg,
                    dangerouslyUseHTMLString: true,
                    message: message,
                    type: type
                })
            },
        },
        watch: {
            parent_id(){
                this.getAllUsers();
            }
        }
    }
</script>

<style scoped>

</style>