<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAddMerchant" v-permission="'merchant/create'" size="small">创建商户</el-button>
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

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getAllMerchant" />
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'Edit Merchant':'New Merchant'">
            <el-form :model="client" label-width="15%" label-position="right">
                <el-form-item label="商户名称">
                    <el-input v-model="client.account" placeholder="商户名称" />
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
    import { getAllMerchant,editMerchant,addMerchant,getMerchant,deleteMerchant } from '@/api/merchant'
    import { mapGetters } from 'vuex'


    const defaultMerchant = {
        id:'',
        account:'',
        status:true,
    };

    export default {
        name: "MerchantIndex",
        data(){
            return {
                client: Object.assign({}, defaultMerchant),
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
            this.getAllMerchant();
        },
        methods:{
            async getAllMerchant(){
                this.loading =  true;

                let data = this.listQuery;
                data.parent_id = this.parent_id;
                let result = await getAllMerchant(data);

                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.client_list = result.data.data.client_list;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAddMerchant(){
                this.client = Object.assign({}, defaultMerchant)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                this.loading =  true;
                let current_client = await getMerchant(scope.row.id);
                this.client = current_client.data.data;
                this.client.status = (this.client.status==1)?true:false;
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
                    let result = await deleteMerchant( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAllMerchant();
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
                    response = await editMerchant(this.client)
                }else{
                    response = await addMerchant(this.client)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>Merchant name: ${this.client.title}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllMerchant();

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
                this.getAllMerchant();
            }
        }
    }
</script>

<style scoped>

</style>
