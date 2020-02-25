<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAdd" v-permission="'payment_channel/create'" size="small">创建支付通道</el-button>
            </div>

            <el-table :data="payment_channel_list" style="width: 100%;margin-top:30px;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="名称" prop="name"></el-table-column>
                <el-table-column align="header-center" label="支付渠道名称" prop="payment_category_name"></el-table-column>
                <el-table-column align="header-center" label="支付通道参数" prop="channel_param"></el-table-column>
                <el-table-column align="header-center" label="支付类型参数" prop="methods_param"></el-table-column>
                <el-table-column align="header-center" label="状态" >
                    <template slot-scope="scope">
                        <el-tag type="success" v-if="scope.row.status">正常</el-tag>
                        <el-tag type="danger" v-else>禁用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope" >
                        <el-button type="primary" size="small" v-permission="'payment_channel/edit'" @click="handleEdit(scope)">Edit</el-button>
                        <el-button type="danger" size="small" v-permission="'payment_channel/delete'" @click="handleDelete(scope)">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'编辑支付通道':'新建支付通道'">
            <el-form :model="payment_channel" label-width="15%" label-position="right">
                <el-form-item label="名称">
                    <el-input v-model="payment_channel.name" placeholder="中文名称" />
                </el-form-item>
                <el-form-item label="支付渠道">
                    <el-select v-model="payment_channel.payment_category_id" placeholder="请选择" @change="changeCategory">
                        <el-option
                            v-for="item in payment_categorys"
                            :key="item.id"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>

                <el-form-item :label="item.name"  v-for="(item,key) in payment_channel.channel_param" :key="key">
                    <el-input v-model="item.value"/>
                </el-form-item>

                <el-form-item label="支付渠道">
                    <el-row :gutter="5" v-for="(method,key) in payment_channel.methods_param" :key="key" style="margin-bottom:5px;">
                        <el-col :span="4" style="text-align: center">{{method.name}}</el-col>
                        <el-col :span="15"><el-input  v-model="method.rate" placeholder="费率(%)"></el-input></el-col>
                        <el-col :span="4" style="text-align: center">
                            <el-switch
                                v-model="method.status"
                                active-color="#13ce66"
                                inactive-color="#ddd">
                            </el-switch>
                        </el-col>
                    </el-row>
                </el-form-item>

                <el-form-item label="是否启用">
                    <el-switch
                        v-model="payment_channel.status"
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
    import { getAllChannel,createChannel,getChannel,editChannel,deleteChannel } from '@/api/payment_channel'


    const defaultChannel = {
        id: '',
        name:'',
        payment_category_id: '',
        top_user_ids:[],
        channel_param:[],
        methods_param:[],
        status: true,
    };

    export default {
        name: "method",
        data(){
            return {
                payment_channel: Object.assign({}, defaultChannel),
                payment_channel_list:[],
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
                payment_categorys:[],
                current_payment_categroy:{},
            };
        },
        directives: { permission },
        created() {
            this.getAllChannel();
        },
        methods:{
            changeCategory( id ){
                this.current_payment_categroy = this.payment_categorys.find(item => {
                    return item.id === id;
                });

                // 增加支付通道参数
                let new_data = [];
                for(let item of this.current_payment_categroy.param){
                    let old_data = this.payment_channel.channel_param[item.ident];
                    new_data.push({
                        'ident' : item.ident,
                        'name'  : item.name,
                        'value' : (old_data!=undefined && old_data != 'null')?old_data:'',
                    });
                }
                this.payment_channel.channel_param = new_data;

                // 增加支付类型
                for(let m in this.current_payment_categroy.methods){
                    let method = this.current_payment_categroy.methods[m];

                    let hasKey = false;
                    for(let x in this.payment_channel.methods_param){
                        if( this.payment_channel.methods_param[x].ident == method.ident ){
                            hasKey = true;
                            this.payment_channel.methods_param[x].name = method.name;
                            break;
                        }
                    }

                    if( !hasKey ){
                        method.status = false;
                        method.rate = 0;
                        this.payment_channel.methods_param.push(Object.assign({}, method));
                    }
                }
                console.log(this.payment_channel.methods_param,this.current_payment_categroy.methods);
            },
            async getAllChannel(){
                this.loading =  true;

                let result = await getAllChannel();

                if( result.data.code == 1 ){
                    this.payment_channel_list = result.data.data.payment_channel;
                    this.payment_categorys = result.data.data.payment_category;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAdd(){
                this.payment_channel = Object.assign({}, defaultChannel)
                this.current_payment_categroy = {}
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope ){
                this.loading =  true;
                let result = await getChannel(scope.row.id);;//
                this.payment_channel = result.data.data;
                this.changeCategory(this.payment_channel.payment_category_id);
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
                    let result = await deleteChannel( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAllChannel();
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

                // 数据重组
                let request_data = {
                    id: this.payment_channel.id,
                    name:this.payment_channel.name,
                    payment_category_id: this.payment_channel.payment_category_id,
                    top_user_ids:[],
                    channel_param:{},
                    methods_param:[],
                    status: true,
                };
                // 重组支付渠道参数,只提交必要参数
                let new_channel_param = {};
                for(let x of this.payment_channel.channel_param){
                    new_channel_param[x.ident] = x.value;
                }
                request_data.channel_param = new_channel_param;

                // 重组支付接口参数,只提交必要参数
                let new_methods_param = [];
                for(let x of this.payment_channel.methods_param){
                    new_methods_param.push({
                        rate:x.rate,
                        ident:x.ident,
                        status:x.status,
                    });
                }
                request_data.methods_param = new_methods_param;

                if (isEdit) {
                    response = await editChannel(request_data)
                }else{
                    response = await createChannel(request_data)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>支付通道: ${this.payment_channel.name}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllChannel();

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
