<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <el-form ref="form" :model="search" label-width="80px" size="small">
                <el-row :gutter="80" >
                    <el-col :span="8">
                        <el-form-item label="订单编号">
                            <el-input v-model="search.id"></el-input>
                        </el-form-item>

                        <el-form-item label="用户搜索">
                            <el-radio v-model="search.id" label="1">手动输入</el-radio>
                            <el-radio v-model="search.id" label="2">总代列表</el-radio>
                        </el-form-item>

                        <el-form-item label="用户名">
                            <el-input v-model="search.id"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="账变时间">
                            <el-date-picker
                                v-model="search.time"
                                type="datetimerange"
                                range-separator="至"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期">
                            </el-date-picker>
                        </el-form-item>

                        <el-form-item label="管理员">
                            <el-select v-model="search" placeholder="请选择" style="display: block;">
                                <el-option
                                    v-for="item in admin_list"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="账变金额">
                            <el-input v-model="search.id"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">

                        <!--
                        <el-form-item label="用户组别">
                            <el-select v-model="search" placeholder="请选择" style="display: block;">
                                <el-option
                                    v-for="item in user_group"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        -->

                        <el-form-item label="IP地址">
                            <el-input v-model="search.id"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row justify="center" type="flex">
                    <el-button type="primary" icon="el-icon-search" @click="getDeposits" size="small">搜索</el-button>
                    <el-button type="warning" icon="el-icon-circle-plus-outline" @click="handleExport" size="small">导出</el-button>
                </el-row>
            </el-form>
        </div>

        <div class="container" style="margin-top:20px;">
            <el-table :data="deposit_list" style="width: 100%;" border >
                <el-table-column align="center" label="订单号" prop="id"></el-table-column>
                <el-table-column align="header-center" label="商户号" prop="account"></el-table-column>
                <el-table-column align="header-center" label="支付通道" prop="payment_channel_name"></el-table-column>
                <el-table-column align="header-center" label="支付类型" prop="payment_method_name"></el-table-column>
                <el-table-column align="header-center" label="金额" prop="amount"></el-table-column>
                <el-table-column align="header-center" label="实际支付金额" prop="">
                    <template slot-scope="scope">
                        {{scope.row.real_amount>0?scope.row.real_amount:scope.row.manual_amount}}
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="商户订单号" prop="merchant_order_no"></el-table-column>
                <el-table-column align="header-center" label="账变ID" prop="order_id"></el-table-column>
                <el-table-column align="header-center" label="会计" prop="accountant_admin"></el-table-column>
                <el-table-column align="header-center" label="出纳" prop="cash_admin"></el-table-column>
                <el-table-column align="header-center" label="推送状态" prop="push_status">
                    <template slot-scope="scope">
                        <el-tag type="info" v-if="scope.row.push_status==0">未推送</el-tag>
                        <el-tag type="success" v-else-if="scope.row.push_status>0">成功</el-tag>
                        <el-tag type="danger" v-else>失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="状态" >
                    <template slot-scope="scope">
                        <el-tag type="info" v-if="scope.row.status==0">支付中</el-tag>
                        <el-tag type="warning" v-else-if="scope.row.status==1">已审核</el-tag>
                        <el-tag type="success" v-else-if="scope.row.status==2">充值成功</el-tag>
                        <el-tag type="danger" v-else>充值失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="到账时间" prop="done_at"></el-table-column>
                <el-table-column align="header-center" label="申请时间" prop="created_at"></el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope">
                        <el-button type="primary" size="small" v-permission="'deposits/detail'" v-if="scope.row.status==2||scope.row.status==3" @click="handleView(scope,1)">详情</el-button>
                        <el-button type="danger" size="small" v-permission="'deposits/verify'" v-else-if="scope.row.status==1" @click="handleView(scope,2)">确认充值</el-button>
                        <el-button type="warning" size="small" v-permission="'deposits/deal'" v-else @click="handleView(scope,3)">人工审核</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getDeposits" />
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType!=='deal'?'订单明细':'人工处理'">
            <el-form  label-position="right" label-width="140px" >
                <el-row>
                    <el-col :span="12"><el-form-item label="商户号：">{{deposit.merchant_account}}</el-form-item></el-col>
                    <el-col :span="12"><el-form-item label="受付账号：">{{deposit.account_number}}</el-form-item></el-col>
                </el-row>
                <el-row>
                    <el-col :span="12"><el-form-item label="受付渠道：">{{deposit.payment_channel_name}}</el-form-item></el-col>
                    <el-col :span="12"><el-form-item label="受付类型：">{{deposit.payment_method_name}}</el-form-item></el-col>
                </el-row>
                <el-row>
                    <el-col :span="12"><el-form-item label="充值时间：">{{deposit.created_at}}</el-form-item></el-col>
                    <el-col :span="12"><el-form-item label="充值地址：">{{deposit.ip}}</el-form-item></el-col>
                </el-row>
                <el-row>
                    <el-col :span="12"><el-form-item label="充值金额：">{{deposit.amount}}<el-button size="small" class="copyBtn">复制</el-button></el-form-item></el-col>
                    <el-col :span="12"><el-form-item label="实际充值金额：">{{deposit.real_amount}}</el-form-item></el-col>
                </el-row>

                <template v-if="dialogType==='verify'||dialogType==='detail'">
                    <el-row>
                        <el-col :span="12"><el-form-item label="人工确认充值金额：">{{deposit.manual_amount}}</el-form-item></el-col>
                        <el-col :span="12"><el-form-item label="人工确认手续费：">{{deposit.merchant_fee}}</el-form-item></el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="12"><el-form-item label="人工输入附言：">{{deposit.manual_postscript}}</el-form-item></el-col>
                        <el-col :span="12"><el-form-item label="外部交易ID：">{{deposit.third_order_no}}</el-form-item></el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="12"><el-form-item label="会计：">{{deposit.accountant_admin}}</el-form-item></el-col>
                        <el-col :span="12"><el-form-item label="受理时间：">{{deposit.deal_at}}</el-form-item></el-col>
                    </el-row>
                    <el-row v-if="dialogType==='verify'">
                        <el-col :span="24">
                            <el-form-item label="审核结果：">
                                <el-radio v-model="verify_form.status" label="2" type="success">通过</el-radio>
                                <el-radio v-model="verify_form.status" label="3" type="error">拒绝</el-radio>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row v-if="dialogType==='verify'">
                        <el-col :span="24">
                            <el-form-item label="操作备注：">
                                <el-input type="textarea" :rows="2" placeholder="请输入内容" v-model="verify_form.admin_remark"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </template>
                <template v-else>
                    <el-row>
                        <el-col :span="12"><el-form-item label="商户手续费：">{{deposit.merchant_fee}}<el-button size="small" class="copyBtn">复制</el-button></el-form-item></el-col>
                        <el-col :span="12"><el-form-item label="第三方手续费：">{{deposit.third_fee}}<el-button size="small" class="copyBtn">复制</el-button></el-form-item></el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="24"><el-form-item label="充值备注：">{{deposit.remark}}</el-form-item></el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="12"><el-form-item label="实际到帐金额："><el-input v-model="deal_form.manual_amount" placeholder="实际到帐金额" /></el-form-item></el-col>
                        <el-col :span="12"><el-form-item label="商户手续费："><el-input v-model="deal_form.merchant_fee" placeholder="用户手续费" /></el-form-item></el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="12"><el-form-item label="附言："><el-input v-model="deal_form.manual_postscript" placeholder="附言" /></el-form-item></el-col>
                        <el-col :span="12"><el-form-item label="第三方流水："><el-input v-model="deal_form.third_order_no" placeholder="第三方或银行流水" /></el-form-item></el-col>
                    </el-row>
                </template>
            </el-form>
            <div style="text-align:right;">
                <el-button type="danger" @click="dialogVisible=false">Cancel</el-button>
                <el-button type="primary" @click="confirm">Confirm</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js'    // 权限判断指令
    import Pagination from '@/components/Pagination'            // Secondary package based on el-pagination
    import { getDeposits,getDetail,putDeal,putVerify } from '@/api/deposits'

    const defaultForm = {
        id: '',
        manual_amount:'',
        merchant_fee:'',
        manual_postscript:'',
        third_order_no:'',
    };

    const defaultVerifyForm = {
        id: '',
        status: '',
        admin_remark: '',
    }

    export default {
        name: "deposits",
        data(){
            return {
                search:{
                    id: '',
                    time : [new Date(2000, 10, 10, 10, 10), new Date(2000, 10, 11, 10, 10)],
                },
                admin_list: [],
                deposit:[],
                deal_form:Object.assign({}, defaultForm),
                verify_form:Object.assign({}, defaultVerifyForm),
                deposit_list: [],
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
        },
        components: { Pagination },
        directives: { permission },
        created(){
            this.getDeposits();
        },
        methods:{
            async getDeposits(){
                this.loading =  true;

                let data = this.listQuery;
                data.parent_id = this.parent_id;
                let result = await getDeposits(data);

                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.deposit_list = result.data.data.deposits;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            /**
             * 详情
             * @param data
             * @param type 类型 1:查看  2:人工审核
             */
            async handleView( scope , type ){
                this.loading =  true;
                let current_deposit = await getDetail(scope.row.id);//
                this.deposit = current_deposit.data.data;
                if(type == 1){
                    this.dialogType = 'detail';
                }else if(type == 2){
                    this.dialogType = 'verify';
                    this.verify_form.id = this.deposit.id;
                }else{
                    this.dialogType = 'deal';
                    this.deal_form.id = this.deposit.id;
                }
                this.dialogVisible = true
                this.loading =  false;
            },
            async confirm(){
                this.dialogVisible = true

                let response;

                if (this.dialogType==='deal') {
                    response = await putDeal(this.deal_form)
                }else if(this.dialogType==='verify'){
                    response = await putVerify(this.verify_form)
                }

                this.dialogVisible = false

                this.getDeposits();

                this.$message({
                    message: response.data.msg,
                    type: response.data.code==1?'success':'error'
                });
            },
            handleExport(){

            }
        },
    }
</script>

<style lang="scss" scoped >
    /deep/.el-dialog{
        .copyBtn{
            float: right;
            margin-top: 4px;
            margin-right: 15px;
        }
        .el-radio{
            .el-radio__label{
                font-size:25px;
            }
            &[type=success]{
                color:green;
            }
            &[type=error]{
                color:red;
            }
        }
    }
</style>
