<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAdd" v-permission="'payment_category/create'" size="small">创建支付渠道</el-button>
            </div>

            <el-table :data="payment_category_list" style="width: 100%;margin-top:30px;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="标识" prop="ident"></el-table-column>
                <el-table-column align="header-center" label="名称" prop="name"></el-table-column>
                <el-table-column align="header-center" label="支付类型">
                    <template slot-scope="scope"><!--  prop="methods" prop="param" -->
                        <el-link v-for="(method,key) in scope.row.method_idents" :key="key">{{(key!=0?' ,':'')+method}}</el-link>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="参数" prop="param">
                    <template slot-scope="scope">
                        <el-tooltip v-for="(item,key) in scope.row.param" :key="key" effect="dark" :content="item.ident" placement="bottom">
                            <el-link >{{(key!=0?' ,':'')+item.name}}</el-link>
                        </el-tooltip>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="状态" >
                    <template slot-scope="scope">
                        <el-tag type="success" v-if="scope.row.status">正常</el-tag>
                        <el-tag type="danger" v-else>禁用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope" >
                        <el-button type="primary" size="small" v-permission="'payment_category/edit'" @click="handleEdit(scope)">Edit</el-button>
                        <el-button type="danger" size="small" v-permission="'payment_category/delete'" @click="handleDelete(scope)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'编辑支付渠道':'新建支付渠道'">
            <el-form :model="payment_category" label-width="15%" label-position="right">
                <el-form-item label="标识">
                    <el-input v-model="payment_category.ident" placeholder="英文标识" />
                </el-form-item>
                <el-form-item label="名称">
                    <el-input v-model="payment_category.name" placeholder="中文名称" />
                </el-form-item>
                <el-form-item label="支付渠道">
                    <el-checkbox-group v-model="payment_category.method_idents">
                        <el-checkbox v-for="(method,key) in payment_methods" :label="method.ident" :key="key" >{{method.name}}</el-checkbox>
                    </el-checkbox-group>
                </el-form-item>
                <el-form-item label="参数">
                    <el-row :gutter="5" v-for="(param,key) in payment_category.param" :key="key" style="margin-bottom:5px;">
                        <el-col :span="5"><el-input v-model="param.name" placeholder="字段名称"></el-input></el-col>
                        <el-col :span="5"><el-input v-model="param.ident" placeholder="英文名称"></el-input></el-col>
                        <el-col :span="4">
                            <el-select v-model="param.type" placeholder="字段类型">
                                <el-option
                                    v-for="type in types"
                                    :key="type.id"
                                    :label="type.name"
                                    :value="type.id">
                                </el-option>
                            </el-select>
                        </el-col>
                        <el-col :span="5"><el-input v-model="param.default_value" placeholder="默认值，多个以逗号分割"></el-input></el-col>
                        <el-col :span="2"><el-checkbox v-model="param.require" >必须</el-checkbox></el-col>
                        <el-col :span="2"><el-button type="text" @click="deleteRow(key)">删除</el-button></el-col>
                    </el-row>
                    <el-row :gutter="5">
                        <el-col :span="2"><el-button type="text" @click="addNewRow">增加</el-button></el-col>
                    </el-row>
                </el-form-item>
                <el-form-item label="是否启用">
                    <el-switch
                        v-model="payment_category.status"
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
    import { getAllCategory,createCategory,getCategory,editCategory,deleteCategory } from '@/api/payment_category'

    let defaultParam = {ident:'',name:'',type:'',default_value:''};
    const defaultCategory = {
        id: '',
        ident:'',
        name: '',
        method_idents: [],
        param: [defaultParam],
        status: true,
    };

    export default {
        name: "category",
        data(){
            return {
                payment_category: Object.assign({}, defaultCategory),
                payment_category_list:[],
                payment_methods:[],
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
                types:[],
            };
        },
        directives: { permission },
        created() {
            this.getAllCategory();
        },
        methods:{
            async getAllCategory(){
                this.loading =  true;

                let result = await getAllCategory();

                if( result.data.code == 1 ){
                    this.payment_category_list = result.data.data.payment_category;
                    this.payment_methods = result.data.data.payment_methods;
                    this.types = result.data.data.types;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAdd(){
                this.payment_category = Object.assign({}, defaultCategory)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                this.loading =  true;
                let result = await getCategory(scope.row.id);;//
                this.payment_category = result.data.data;
                this.dialogType = 'edit'
                this.dialogVisible = true
                this.loading =  false;
            },
            handleDelete( scope ){
                this.$confirm('此操作将永久删除该支付渠道, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then( async() => {
                    let result = await deleteCategory( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAllCategory();
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
                    response = await editCategory(this.payment_category)
                }else{
                    response = await createCategory(this.payment_category)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>支付渠道: ${this.payment_category.name}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllCategory();

                this.$notify({
                    title: response.data.msg,
                    dangerouslyUseHTMLString: true,
                    message: message,
                    type: type
                })
            },
            addNewRow(){
                this.payment_category.param.push(Object.assign({}, defaultCategory));
            },
            deleteRow(index){
                this.payment_category.param = this.payment_category.param.filter((param,_index) => _index != index );
            }
        },
        watch: {

        }
    }
</script>

<style scoped>

</style>
