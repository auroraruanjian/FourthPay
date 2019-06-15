<template>
    <div class="app-container" v-loading="loadding">
        <el-button type="primary" @click="handleAddAdmin" v-permission="'admin/create'">创建管理员</el-button>

        <el-table :data="adminList" style="width: 100%;margin-top:30px;" border>
            <el-table-column align="center" label="Role Key" prop="id"></el-table-column>
            <el-table-column align="center" label="用户名" prop="username"></el-table-column>
            <el-table-column align="header-center" label="昵称" prop="nickname"></el-table-column>
            <el-table-column align="header-center" label="是否锁定" prop="is_locked"></el-table-column>
            <el-table-column align="header-center" label="上次登录IP" prop="last_ip"></el-table-column>
            <el-table-column align="header-center" label="上次登录时间" prop="last_time"></el-table-column>
            <el-table-column align="center" label="Operations">
                <template slot-scope="scope" v-if="scope.row.id!=1">
                    <el-button type="primary" size="small" @click="handleEdit(scope)">Edit</el-button>
                    <el-button type="danger" size="small" @click="handleDelete(scope)">Delete</el-button>
                </template>
            </el-table-column>
        </el-table>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'Edit Admin':'New Admin'">
            <el-form :model="admin" label-width="80px" label-position="left">
                <el-form-item label="用户名">
                    <el-input v-model="admin.username" placeholder="用户名" />
                </el-form-item>
                <el-form-item label="昵称">
                    <el-input v-model="admin.nickname" placeholder="昵称" />
                </el-form-item>
                <el-form-item label="密码">
                    <el-input v-model="admin.password" placeholder="密码" show-password/>
                </el-form-item>
                <el-form-item label="锁定">
                    <el-switch v-model="admin.is_locked" ></el-switch>
                </el-form-item>
                <el-form-item label="角色">
                    <el-checkbox-group v-model="admin.role" >
                        <el-checkbox :label="item.id" v-for="(item,key) in rolesList" :key="key">{{item.name}}</el-checkbox>
                    </el-checkbox-group>
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
    import { getAllAdmins,addAdmin,editAdmin,getAdminUser,deleteAdmin } from '@/api/admin'
    import { getAllRoles } from '@/api/role'

    const defaultAdmin = {
        id : '',
        username : '',
        nickname : '',
        password : '',
        is_locked : false,
        role : [],
    };

    export default{
        data(){
            return {
                admin: Object.assign({}, defaultAdmin),
                adminList: [],
                rolesList: [],
                dialogVisible: false,
                dialogType: 'new',
                loadding:false,
            };
        },
        computed: {
        },
        directives: { permission },
        created() {
            this.getAdminUsers();
            this.getRoles();
        },
        methods: {
            async getAdminUsers(){
                let result = await getAllAdmins();
                if( result.data.code == 1 ){
                    this.adminList = result.data.data;
                }else{
                    this.$message.error(result.data.message);
                }
            },
            async getRoles() {
                let result = await getAllRoles()
                if( result.data.code == 1 ){
                    this.rolesList = result.data.data
                }else{
                    this.$message.error(result.data.message);
                }
            },
            handleAddAdmin(){
                this.admin = Object.assign({}, defaultAdmin)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                let current_user = await getAdminUser(scope.row.id);
                this.admin = current_user.data.data;
                this.dialogType = 'edit'
                this.dialogVisible = true
            },
            handleDelete(scope ){
                this.$confirm('此操作将永久删除该管理员, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then( async() => {
                    let result = await deleteAdmin( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAdminUsers();
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
                    response = await editAdmin(this.admin)
                }else{
                    response = await addAdmin(this.admin)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>Admin name: ${this.admin.name}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getRoles();

                this.$notify({
                    title: response.data.msg,
                    dangerouslyUseHTMLString: true,
                    message: message,
                    type: type
                })
            }
        }
    }
</script>