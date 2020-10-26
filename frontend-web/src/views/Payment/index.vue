<template>
    <div class="payment_page">


        <div class="container">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>订单编号: {{pay_info.order_id}}</span>
                    <span style="float: right; padding: 3px 0" >收款商家: {{pay_info.name}}</span>
                </div>
                <el-steps :active="2" align-center style="margin: 50px 0px;">
                    <el-step title="选择商品" ></el-step>
                    <el-step title="确定付款" ></el-step>
                    <el-step title="下单成功" ></el-step>
                </el-steps>
                <el-row style="margin-top: 60px;">
                    <h3 style="color: red;font-size: 20px;">支付金额: <span>￥{{post_form.amount}}</span></h3>
                    <p style="margin-top: 20px;margin-bottom: 15px;font-size: 13px;">请选择付款方式</p>
                </el-row>
                <el-row class="ways">
                    <div class="borders ali_pay" :class="(post_form.payment_method==item.ident?'click_active':'')" v-for="(item,key) in payment_method" :key="key" @click="post_form.payment_method=item.ident">
                        <p>
                            <img :src="'/img/'+item.ident+'.png'" :alt="item.name">
                        </p>
                    </div>
                </el-row>
                <el-row class="pay_row">
                    <span>测试体验商品不会发货</span>
                    <el-button type="primary">立即支付</el-button>
                </el-row>
            </el-card>


        </div>
    </div>
</template>

<script>
export default {
    name: "Payment",
    data(){
        return {
            payment_method:[
                {
                    'ident':'wechat_scan',
                    'name':'微信扫码',
                },
                {
                    'ident':'alipay_scan',
                    'name':'支付宝扫码',
                }
            ],
            pay_info:{
                order_id:'WC2018022351274',
                name:'FouthPay',
            },
            post_form:{
                amount:'0.01',
                payment_method:'wechat_scan',
            }
        };
    },
    mounted() {
        this.$store.dispatch('app/toggleHeaderModel', 'light');
    }
}
</script>

<style scoped lang="scss">
.payment_page{
    background-color: #f5f7f9;
    padding-top: 130px;
    padding-bottom: 60px;

    >.container{
        //height: 500px;
        //background: #fff;
        width:960px;
        text-align: left;
        //border: 1px solid #e6e6e6;

        /deep/ .el-card__header {
            padding: 50px 45px 33px 45px;
            border-bottom: 1px solid #EBEEF5;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .ways{
            border: 1px solid #e6e6e6;
            border-left: 0;
            border-right: 0;
            padding-top: 20px;
            padding-bottom: 20px;
            padding-left: 10px;

            .borders {
                width: 140px;
                height: 45px;
                border: 1px solid #f4f4f4;
                float: left;
                margin-right: 20px;
                line-height: 45px;
                display: table;
                cursor: pointer;

                p {
                    display: table-cell;
                    text-align: center;
                    vertical-align: middle;

                    img{
                        vertical-align: middle;
                    }
                }
            }

            .click_active {
                border: 1px solid #3497FC;
            }
        }

        .pay_row{
            text-align: right;
            float: right;
            margin: 25px 0px;
            color: #777777;
            >span{
                font-size: 12px;
                margin-right: 10px;
            }
        }
    }
}
</style>
