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
                <el-table-column align="header-center" label="支付通道参数">
                    <template slot-scope="scope">
                        {{scope.row.channel_param.appid}},{{scope.row.channel_param.merchant_id}}
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="支付类型">
                    <template slot-scope="scope">
                        <el-tooltip v-for="(item,key) in scope.row.detail" :key="key" effect="dark" :content="'限额：'+item.min_amount+'元-'+item.max_amount+'元，费率：'+item.rate+'%'" placement="bottom">
                            <el-tag v-if="item.status">{{item.name}}</el-tag>
                            <el-tag type="info" v-else>{{item.name}}</el-tag>
                        </el-tooltip>
                    </template>r
                </el-table-column>
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
                    <el-select v-model="payment_channel.payment_category_id" placeholder="请选择" @change="changeCategory" :disabled="dialogType==='edit'">
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
                    <!--
                    <el-row :gutter="5" style="margin-bottom:5px;">
                        <el-col :span="4" style="text-align: center">接口名称</el-col>
                        <el-col :span="15" style="text-align: center">费率</el-col>
                        <el-col :span="4" style="text-align: center">状态</el-col>
                    </el-row>
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
                    -->
                    <el-table class="no_padding" :data="payment_channel.detail"  style="width: 100%" size="medium" >
                        <el-table-column type="expand">
                            <template slot-scope="scope">
                                <el-form label-position="right" label-width="100px"  class="demo-table-expand">
                                    <el-form-item label="启用的总代" >
                                        <el-checkbox-group v-model="scope.row.top_merchant_ids"><!--v-model=""-->
                                            <el-checkbox v-for="top_user in top_users" :label="top_user.id" :key="top_user.id" >{{top_user.username}}</el-checkbox>
                                        </el-checkbox-group>
                                    </el-form-item>
                                    <el-form-item label="银行" ><!--v-if="scope.row.ident=='bank'"-->
                                        <el-checkbox-group v-model="scope.row.extra.banks"><!--v-model=""-->
                                            <el-checkbox v-for="(bank,key) in bank_list" :label="bank.ident" :key="key" >{{bank.name}}</el-checkbox>
                                        </el-checkbox-group>
                                    </el-form-item>
                                    <el-form-item label="时间范围">
                                        <el-time-picker
                                            size="medium"
                                            v-model="scope.row.start_time"
                                            default-value="00:00:00"
                                            placeholder="开始时间">
                                        </el-time-picker>
                                        <el-time-picker
                                            size="medium"
                                            v-model="scope.row.end_time"
                                            default-value="00:00:00"
                                            placeholder="结束时间">
                                        </el-time-picker>
                                    </el-form-item>
                                </el-form>
                            </template>
                        </el-table-column>
                        <el-table-column
                            type="selection"
                            width="46">
                        </el-table-column>
                        <el-table-column prop="payment_method_name" label="接口名称" width="150"></el-table-column>
                        <el-table-column label="第三方费率(%)" width="150">
                            <template slot-scope="scope">
                                <el-input size="medium"  v-model="scope.row.rate" placeholder="第三方收取平台的费率"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column label="限额">
                            <template slot-scope="scope">
                                <el-input size="medium" placeholder="最高金额"  v-model="scope.row.max_amount" class="amount_input"></el-input>
                                <el-input size="medium" placeholder="最低金额" v-model="scope.row.min_amount" class="amount_input"></el-input>
                            </template>
                        </el-table-column>
                        <el-table-column label="状态" width="100">
                            <template slot-scope="scope">
                                <el-switch
                                    v-model="scope.row.status"
                                    active-color="#13ce66"
                                    inactive-color="#ddd">
                                </el-switch>
                            </template>
                        </el-table-column>
                    </el-table>
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
        channel_param:[],
        detail:[],
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
                // 支付渠道列表
                payment_categorys:[],
                // 当前选择的支付渠道
                current_payment_categroy:{},
                // 银行列表
                bank_list:[
                    {
                        ident:'icbc',
                        name:'工商银行'
                    },
                    {
                        ident:'abc',
                        name:'农行'
                    }
                ],
                // 启用的总代
                top_users:[
                    {
                        id:1,
                        username:'agent1',
                    },
                    {
                        id:2,
                        username:'agent2'
                    }
                ]
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
                        'value' : (typeof(old_data)!='undefined' && old_data != null)?old_data:'',
                    });
                }
                this.payment_channel.channel_param = new_data;

                // 增加支付类型
                let new_methods = [];
                for(let m in this.current_payment_categroy.method_idents){
                    let method = this.current_payment_categroy.method_idents[m];

                    let old_method = null;
                    for(let x in this.payment_channel.detail){
                        if( this.payment_channel.detail[x].payment_method_id == method.payment_method_id ){
                            old_method = this.payment_channel.detail[x];
                            //console.log(old_method);
                            break;
                        }
                    }

                    new_methods.push( this.make_detail_data(false,old_method!=null?{...old_method,...method}:method) );
                }
                this.payment_channel.detail = new_methods;
                //console.log(this.payment_channel.detail,this.current_payment_categroy.methods);
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
                    channel_param:{},
                    detail:[],
                    status: true,
                };
                // 重组支付渠道参数,只提交必要参数
                let new_channel_param = {};
                for(let x of this.payment_channel.channel_param){
                    new_channel_param[x.ident] = x.value;
                }
                request_data.channel_param = new_channel_param;

                // 重组支付接口参数,只提交必要参数
                let new_detail = [];
                for(let x of this.payment_channel.detail){
                    new_detail.push(this.make_detail_data(true,x));
                }
                request_data.detail = new_detail;

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
            make_detail_data(isSubmit =false, old_data=null ){
                let now_date = new Date();
                let date_str = now_date.getFullYear() + '-' + now_date.getMonth() + '-' + now_date.getDay();

                let data = {
                    id:(old_data!=null && typeof(old_data.id)!='undefined' && old_data.id != null)?old_data.id:'',
                    top_merchant_ids:(old_data!=null && typeof(old_data.top_merchant_ids)!='undefined' && old_data.top_merchant_ids != null)?old_data.top_merchant_ids:[],
                    rate:(old_data!=null && typeof(old_data.rate)!='undefined' && old_data.rate != null)?old_data.rate:0,
                    max_amount:(old_data!=null && typeof(old_data.max_amount)!='undefined' && old_data.max_amount != null)?old_data.max_amount:0,
                    min_amount:(old_data!=null && typeof(old_data.min_amount)!='undefined' && old_data.min_amount != null)?old_data.min_amount:0,
                    status:(old_data!=null && typeof(old_data.status)!='undefined' && old_data.status != null)?old_data.status:true,
                    payment_method_id:(old_data!=null && typeof(old_data.payment_method_id)!='undefined' && old_data.payment_method_id != null)?old_data.payment_method_id:'',
                    extra:{
                        banks:(old_data!=null && (old_data.banks)!='undefined' && old_data.banks != null)?old_data.banks:[],
                    }
                };

                let start_time = (old_data!=null && typeof(old_data.start_time)!='undefined' && old_data.start_time != null)?old_data.start_time:'00:00:00',
                    end_time = (old_data!=null && typeof(old_data.end_time)!='undefined' && old_data.end_time != null)?old_data.end_time:'00:00:00';

                data.start_time = (typeof(start_time)=='object')?start_time:new Date(date_str +' '+ start_time);
                data.end_time = (typeof(end_time)=='object')?end_time:new Date(date_str +' '+ end_time);

                if( !isSubmit ){
                    data.payment_method_ident = (old_data!=null && typeof(old_data.payment_method_ident)!='undefined' && old_data.payment_method_ident != null)?old_data.payment_method_ident:'';
                    data.payment_method_name = (old_data!=null && typeof(old_data.payment_method_name)!='undefined' && old_data.payment_method_name != null)?old_data.payment_method_name:'';
                    data.payment_method_status = (old_data!=null && typeof(old_data.payment_method_status)!='undefined' && old_data.payment_method_status != null)?old_data.payment_method_status:'';
                }else{
                    console.log(data.start_time);
                    data.start_time = this.time_format(data.start_time);
                    data.end_time = this.time_format(data.end_time);
                }
                //console.log(old_data);
                return data;
            },
            time_format( date ){
                let hour = date.getHours();
                if( hour<10 ) hour += '0';

                let min = date.getMinutes();
                if( min<10 ) min += '0';

                let second = date.getSeconds();
                if( second<10 ) second += '0';

                return hour+':'+min+':'+second;
            }
        },
        watch: {

        }
    }
</script>

<style lang="scss" scope>
    .el-table.no_padding{
        th,.el-table__row td{
            padding: 2px;
        }
        .amount_input {
            width: 100px;
        }
    }
    .cell {
        .el-tag{
            margin-left: 5px;
            :first-child{
                margin-left: 0px;
            }
        }
    }
</style>
