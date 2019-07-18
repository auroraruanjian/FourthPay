<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAddClient" v-permission="'client/create'" size="small">创建商户</el-button>
            </div>

            <el-table :data="client_list" style="width: 100%;margin-top:30px;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="商户名称" prop="account"></el-table-column>
                <el-table-column align="header-center" label="状态" >
                    <template slot-scope="scope">
                        <el-tag type="success" v-if="scope.row.status">正常</el-tag>
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

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getAllClient" />
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'Edit Client':'New Client'">
            <el-form :model="client" label-width="15%" label-position="right">
                <el-form-item label="配置标题">
                    <el-input v-model="client.account" placeholder="配置标题" />
                </el-form-item>
                <el-form-item label="是否启用">
                    <el-switch
                            v-model="client.status"
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
    import { getAllClient,editClient,addClient,getClient,deleteClient } from '@/api/client'
    import { mapGetters } from 'vuex'


    const defaultClient = {
        id:'',
        account:'',
        status:true,
    };

    export default {
        name: "merchantIndex",
        data(){
            return {
                client: Object.assign({}, defaultClient),
                client_list: [],
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
            this.getAllClient();
        },
        methods:{
            async getAllClient(){
                this.loading =  true;

                let data = this.listQuery;
                data.parent_id = this.parent_id;
                let result = await getAllClient(data);

                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.client_list = result.data.data.client_list;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAddClient(){
                this.client = Object.assign({}, defaultClient)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                this.loading =  true;
                let current_client = await getClient(scope.row.id);
                this.client = current_client.data.data;
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
                    let result = await deleteClient( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAllClient();
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
                    response = await editClient(this.client)
                }else{
                    response = await addClient(this.client)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>Client name: ${this.client.title}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllClient();

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
                this.getAllClient();
            }
        }
    }
</script>

<style scoped>

</style>