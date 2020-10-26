<template>
    <div class="index_page">
        <swiper ref="mySwiper" :options="swiperOptions">
            <swiper-slide style="background: url(https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/cloudstrip.png) no-repeat bottom center #353a4d;">
                <div class="container">
                    <h1 style="margin-top: 2.2rem;display: inline-block;">微信支付服务商系统提供商</h1>
                    <p>为微信支付服务商/支付宝ISV提供专业的支付清算系统</p>
                    <el-row><el-button type="primary" round @click="testPay">立即体验支付</el-button></el-row>
                    <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/slider-1.jpg" width="900px">
                </div>
            </swiper-slide>
            <swiper-slide style="background: #f8f8f8;" class="second_slide">
                <div class="container " style="padding-top: 60px;">
                    <h3 >仅需三步快速启动微信支付服务商赚钱模式</h3>
                    <p>告别各种坑，在专业人士指导下快速开启微信支付宝ISV服务商赚钱模式</p>
                    <el-row>
                        <el-col :span="8">
                            <div class="text-center index-img-circle">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/create.svg">
                            </div>
                            <h5 class=" text-center mt-3">支付系统签约</h5>
                        </el-col>
                        <el-col :span="8">
                            <div class="text-center index-img-circle">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/connect.svg">
                            </div>
                            <h5 class=" text-center mt-3">支付系统签约</h5>
                        </el-col>
                        <el-col :span="8">
                            <div class="text-center index-img-circle">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/deploy.svg">
                            </div>
                            <h5 class=" text-center mt-3">发展商户营业</h5>
                        </el-col>
                    </el-row>
                </div>

                <section class="container" >
                    <h3 class="display-6 text-center ">服务商支付通道</h3>
                    <p class="text-center text-muted">包含大部分微信服务商和支付宝ISV支付通道</p>
                    <el-row style="margin-bottom: 20px; ">
                        <el-col :span="8">
                            <div class="d-flex">
                                <div class="mr-4 simple-svg-wrapper"><img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/xiaowei.svg"></div>
                                <div><h5>小微商户通道</h5> <p class="text-opacity-6 text-font-size-sm text-line-height">无营业执照的个人线下商户签约</p></div>
                            </div>
                        </el-col>
                        <el-col :span="8">
                            <div class="d-flex">
                                <div class="mr-4 simple-svg-wrapper"><img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/shanghu.svg"></div>
                                <div><h5>特约商户通道</h5>
                                    <p class="text-opacity-6 text-font-size-sm text-line-height">个体户或企业签约特约商户，支持线上线下交易。</p>
                                </div>
                            </div>
                        </el-col>
                        <el-col :span="8">
                            <div class="d-flex">
                                <div class="mr-4 simple-svg-wrapper"><img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/ecommerce.svg"></div>
                                <div><h5>电商平台通道</h5>
                                    <p class="text-opacity-6 text-font-size-sm text-line-height">个人/个体户/企业签约特约商户，支持线上电商交易，支持分账。</p>
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                    <el-row>
                        <el-col :span="8">
                            <div class="d-flex">
                                <div class="mr-4 simple-svg-wrapper"><img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/kuajing.svg"></div>
                                <div><h5>跨境支付通道</h5>
                                    <p class="text-opacity-6 text-font-size-sm text-line-height">境外机构服务商，发展境外商户，支持多种货币结算。</p>
                                </div>
                            </div>
                        </el-col>
                        <el-col :span="8">
                            <div class="d-flex">
                                <div class="mr-4 simple-svg-wrapper"><img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/alipay.svg"></div>
                                <div><h5>支付宝通道</h5>
                                    <p class="text-opacity-6 text-font-size-sm text-line-height">支付宝个人/企业，手机和APP支付通道。</p>
                                </div>
                            </div>
                        </el-col>
                        <el-col :span="8">
                            <div class="d-flex">
                                <div class="mr-4 simple-svg-wrapper"><img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/saoma.svg"></div>
                                <div><h5>其他支付通道</h5>
                                    <p class="text-opacity-6 text-font-size-sm text-line-height">可定制其他第三方支付通道，如京东支付，和包支付，翼支付等通道。</p>
                                </div>
                            </div>
                        </el-col>
                    </el-row>
                </section>
            </swiper-slide>
            <swiper-slide style="background-color: #fff;" class="third_slide">
                <div class="container">
                    <h3 >核心技术</h3>
                    <p class="text-center text-muted">采用Java+SpringBoot+JPA+MySQL+Spring Cloud分布式技术</p>
                    <el-row>
                        <el-col :span="8">
                            <div class="text-right mt-5">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/fuzhai.svg">
                                <h6 class="font-16 color-3C3C50 pt-20">分布式微服务架构</h6>
                                <div class="color-878F92 mt-2">基于Spring Boot + Dubbo框架实现支持分布式部署，微服务架构</div>
                            </div>
                            <div class="text-right mt-5">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/jhpay.svg">
                                <h6 class="font-16 color-3C3C50 pt-20">聚合支付</h6>
                                <div class="color-878F92 mt-2">采用多码合一聚合支付模式，提高用户付款成功率</div>
                            </div>
                            <div class="text-right mt-5">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/bingfa.svg">
                                <h6 class="font-16 color-3C3C50 pt-20">高并发处理能力</h6>
                                <div class="color-878F92 mt-2">系统对高并发特殊技术处理，满足日交易量百万笔需求</div>
                            </div>
                        </el-col>
                        <el-col :span="8">
                            <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/homepage_tracup_middle_pic2.png" class="d-none d-md-block d-xs-none">
                            <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/homepage_tracup_middle_pic1.png" class="d-none d-md-block d-xs-none">
                        </el-col>
                        <el-col :span="8">
                            <div class="text-left mt-5">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/kuozhan.svg">
                                <h6 class="font-16 color-3C3C50 pt-20">支付通道扩展</h6>
                                <div class="color-878F92 mt-2">完善的支付通道架构，可随时扩展支付通道、以及通道子账号的接口轮询</div>
                            </div>
                            <div class="text-left mt-5">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/dashboard.svg">
                                <h6 class="font-16 color-3C3C50 pt-20">数据统计</h6>
                                <div class="color-878F92 mt-2">多维度常用数据统计图表，让您从不同角度清晰了解数据状态</div>
                            </div>
                            <div class="text-left mt-5">
                                <img src="https://www.xunhuweb.com/wp-content/themes/xunhuweb/images/ui.svg">
                                <h6 class="font-16 color-3C3C50 pt-20">负载均衡</h6>
                                <div class="color-878F92 mt-2">分布在全球的亚马逊云，微软云，阿里云，腾讯云高性能服务器负载均衡</div>
                            </div>
                        </el-col>
                    </el-row>
                    <!--
                    <div class="container mt-2">
                        <div class="row homepage-tracup-introduce hidden-sm hidden-xs mt-60">
                            <div class="col-lg-1 col-md-1">
                            </div>
                            <div class="col-lg-3 col-md-3 homepage-tracup-introduce-item pt50">

                            </div>
                            <div class="col-lg-4 col-md-4 homepage-tracup-introduce-bigpic">
                            </div>
                            <div class="col-lg-3 col-md-3 homepage-tracup-introduce-item pt50">

                            </div>
                        </div>
                    </div>
                    -->
                </div>
            </swiper-slide>
            <swiper-slide style="background-color: #000;">
                <div class="container" >
                    Slide 4
                </div>
            </swiper-slide>
            <swiper-slide  style="background-color: #000;">
                <div class="container">
                    Slide 5
                </div>
            </swiper-slide>
            <div class="swiper-pagination" slot="pagination"></div>
        </swiper>

    </div>
</template>

<script>
import { mapGetters } from 'vuex'

import { Swiper, SwiperSlide, directive } from 'vue-awesome-swiper'
import 'swiper/css/swiper.css'

export default {
    name: "Index",
    data(){
        console.log(window.outerHeight,window.innerHeight);
        let _this = this;

        return {
            swiperOptions: {
                direction: 'vertical',
                slidesPerView: 1,
                // spaceBetween : 60,
                mousewheel: {
                    //releaseOnEdges: true,
                },
                autoHeight: true,
                height: window.innerHeight,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true
                },
                on:{
                    slideChange: function(){
                        if( this.activeIndex >= 1 ){
                            console.log(this.activeIndex);
                            _this.$store.dispatch('app/toggleHeaderModel', 'light');
                        }else{
                            _this.$store.dispatch('app/toggleHeaderModel', 'dark');
                        }
                    },
                },
            }
        };
    },
    components: {
        Swiper,
        SwiperSlide
    },
    directives: {
        swiper: directive
    },
    computed: {
        ...mapGetters([
            'header_model',
        ]),
    },
    methods:{
        testPay(){
            this.$router.push({path:'/payment'});
        },
    },
    mounted() {
        console.log('Current Swiper instance object', this.swiper)
        //this.swiper.slideTo(3, 1000, false)
    }
}
</script>

<style scoped lang="scss">
.swiper-container {
    width: 100%;
    height: 100%;
    margin-left: auto;
    margin-right: auto;

    .swiper-slide{
        font-size: 2.4rem;
        color: #fff;
        line-height: 5rem;
        overflow: hidden;

        >.container{
            margin-top:80px;
        }

        p{
            font-size: 1rem;
            color: #6c757d;
            line-height: 1.2rem;
        }
    }

    .second_slide{
        font-weight: 500;
        line-height: 1.5rem;
        color: #6c757d;

        .container{
            padding-top: 10px;
            padding-bottom: 10px;

            >h3{
                font-size: 1.75rem;
                margin-bottom: .5rem;
                font-family: inherit;
                color: #212529;
                line-height: 3rem;
            }
            >p{
                margin-bottom: 3rem;
                font-size: 1.1rem;
            }
        }
        .el-col{
            .index-img-circle{
                border: 8px solid #f9bf3be6;
                background: linear-gradient(30deg,#22313f,#3c374b);
                border-radius: 50%;
                margin: auto;
                width: 180px;

                >img{
                    padding: 40px;
                    width: 180px;
                    horizontal-align: middle;
                    box-sizing: border-box;
                }
            }
            >h5{
                color: #212529;
                margin-bottom: .5rem;
                font-family: inherit;
                font-weight: 500;
                line-height: 1.2;
                font-size: 1.5rem;
                margin-top: 1rem!important;
            }
            >.d-flex{
                display: flex!important;
                box-sizing: border-box;
                text-align: left;
                color: #212529;

                .simple-svg-wrapper{
                    margin-right: 1.5rem!important;
                }

                h5{
                    font-size: 1.25rem;
                    margin-bottom: 5px;
                }
                p{
                    margin-top: 0;
                    margin-bottom: 1rem;
                    color: #212529;
                    display: block;
                    line-height: 1.5rem;
                    font-weight: normal;
                    font-size: 0.9rem;
                }

            }
        }
    }
    .third_slide{

        .container{
            margin-top:150px;
            >h3{
                color: #212529;
                font-size: 1.8rem;
                font-weight: 500;
            }
        }

    }
}
</style>
