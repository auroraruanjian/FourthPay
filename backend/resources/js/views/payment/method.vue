<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAdd" v-permission="'payment_method/create'" size="small">创建支付类型</el-button>
            </div>

            <el-table :data="payment_method_list" style="width: 100%;margin-top:30px;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="标识" prop="ident"></el-table-column>
                <el-table-column align="header-center" label="名称" prop="name"></el-table-column>
                <el-table-column align="header-center" label="状态" >
                    <template slot-scope="scope">
                        <el-tag type="success" v-if="scope.row.status">正常</el-tag>
                        <el-tag type="danger" v-else>禁用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope" >
                        <el-button type="primary" size="small" v-permission="'payment_method/edit'" @click="handleEdit(scope)">Edit</el-button>
                        <el-button type="danger" size="small" v-permission="'payment_method/delete'" @click="handleDelete(scope)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'编辑支付类型':'新建支付类型'">
            <el-form :model="payment_method" label-width="15%" label-position="right">
                <el-form-item label="标识">
                    <el-input v-model="payment_method.ident" placeholder="英文标识" />
                </el-form-item>
                <el-form-item label="名称">
                    <el-input v-model="payment_method.name" placeholder="中文名称" />
                </el-form-item>
                <el-form-item label="是否启用">
                    <el-switch
                            v-model="payment_method.status"
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
    import { getAllMethod,createMethod,getMethod,editMethod,deleteMethod } from '@/api/payment_method'


    const defaultMethod = {
        id: '',
        ident:'',
        name: '',
        status: true,
    };

    export default {
        name: "method",
        data(){
            return {
                payment_method: Object.assign({}, defaultMethod),
                payment_method_list:[],
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
            };
        },
        directives: { permission },
        created() {
            this.getAllMethod();
        },
        methods:{
            async getAllMethod(){
                this.loading =  true;

                let result = await getAllMethod();

                if( result.data.code == 1 ){
                    this.payment_method_list = result.data.data;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAdd(){
                this.payment_method = Object.assign({}, defaultMethod)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                this.loading =  true;
                let result = await getMethod(scope.row.id);;//
                this.payment_method = result.data.data;
                this.dialogType = 'edit'
                this.dialogVisible = true
                this.loading =  false;
            },
            handleDelete( scope ){
                this.$confirm('此操作将永久删除该支付通道, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then( async() => {
                    let result = await deleteMethod( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAllMethod();
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
                    response = await editMethod(this.payment_method)
                }else{
                    response = await createMethod(this.payment_method)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>支付类型: ${this.payment_method.name}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllMethod();

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
