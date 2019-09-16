<template>
    <div class="navbar">
        <hamburger :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />

        <breadcrumb class="breadcrumb-container" />

        <div class="right-menu">
            <template v-if="device!=='mobile'">
                <!--
                <search id="header-search" class="right-menu-item" />

                <error-log class="errLog-container right-menu-item hover-effect" />
                -->

                <screenfull id="screenfull" class="right-menu-item hover-effect" />

                <!--
                <el-tooltip content="Global Size" effect="dark" placement="bottom">
                  <size-select id="size-select" class="right-menu-item hover-effect" />
                </el-tooltip>
                -->

            </template>
            <el-dropdown class="avatar-container" trigger="click">
                <div class="avatar-wrapper">
                    <img :src="avatar+'?imageView2/1/w/80/h/80'" class="user-avatar">
                    <i class="el-icon-caret-bottom" />
                </div>
                <el-dropdown-menu slot="dropdown" class="user-dropdown">
                    <router-link to="/">
                        <el-dropdown-item>
                            首页
                        </el-dropdown-item>
                    </router-link>
                    <!--
                    <a target="_blank" href="">
                        <el-dropdown-item>Docs</el-dropdown-item>
                    </a>
                    -->
                    <el-link @click.native="changePasswd" :underline="false">
                        <el-dropdown-item>
                            修改密码
                        </el-dropdown-item>
                    </el-link>
                    <el-link @click.native="bindWechat" :underline="false" v-if="!wechat_status">
                        <el-dropdown-item>
                            绑定微信
                        </el-dropdown-item>
                    </el-link>
                    <el-link @click.native="unBindWechat" :underline="false" v-if="wechat_status">
                        <el-dropdown-item>
                            解绑微信
                        </el-dropdown-item>
                    </el-link>
                    <el-link @click.native="bindGoogle" :underline="false" >
                        <el-dropdown-item>
                            绑定Google Key
                        </el-dropdown-item>
                    </el-link>
                    <el-dropdown-item divided>
                        <span style="display:block;" @click="logout">退出</span>
                    </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>

        <el-dialog :visible.sync="wechat.wechat_visible" title="微信绑定" width="320px" @close="wechat_close">
            <div style="text-align: center;" v-loading="wechat.qrcode_loading">
                <img :src="wechat.qrcode" >
            </div>
            <div style="text-align:right;margin-top:10px;">
                <el-button type="danger" @click="wechat.wechat_visible=false">Cancel</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import { mapGetters,mapState } from 'vuex'
    import Breadcrumb from '@/components/Breadcrumb'
    import Hamburger from '@/components/Hamburger'
    import Screenfull from '@/components/Screenfull'
    import QRCode  from 'qrcode'
    import { wechat_login,unbind_wechat } from '@/api/auth'

    export default {
        components: {
            Breadcrumb,
            Hamburger,
            Screenfull
        },
        computed: {
            ...mapGetters([
                'sidebar',
                'avatar',
            ]),
            ...mapState({
                device: state => state.app.device,
                wechat_status:state => state.user.wechat_status,
            }),
        },
        data(){
            return {
                wechat:{
                    interval:null,
                    wechat_visible:false,
                    qrcode:'',
                    state:'',
                    mode:'web',
                    qrcode_loading:'',
                },
            };
        },
        methods: {
            toggleSideBar() {
                this.$store.dispatch('app/toggleSideBar')
            },
            async logout() {
                // await this.$store.dispatch('user/logout')
                // this.$router.push(`/login?redirect=${this.$route.fullPath}`)
                this.$store.dispatch('user/logout').then((response) => {
                    if (response.data.code == 1) {
                        this.$message(response.data.msg);
                    }
                    this.$router.push({path:'/login'});
                }).catch((e) => {
                    console.log(e);
                });
            },
            changePasswd(){},
            async wechatState(){
                let _this = this;

                let res = await wechat_login(_this.wechat.state,_this.wechat.mode);

                console.log(res.data);

                // 绑定成功
                if( res.data.code == 1 ){
                    this.$message( res.data.msg ,'success');
                    this.$store.commit('user/SET_WECHAT_STATUS', true);
                    _this.wechat_close();
                    return true;
                // 刷新二维码
                }else if(res.data.code == 2){
                    let opts = {
                        errorCorrectionLevel: 'H',
                        type: 'image/jpeg',
                        rendererOpts: {
                            quality: 1
                        },
                        width:250,
                        margin:0
                    }

                    _this.wechat.state = res.data.data.state;

                    QRCode.toDataURL(res.data.data.qrcode, opts, function (err, url) {
                        if (err) throw err

                        _this.wechat.qrcode = url;
                    });

                    return true;
                }else{
                    if( this.wechat.state == '' ){
                        this.$message( res.data.msg ,'error');
                    }
                }

                return false;
            },
            wechat_start(){
                let _this = this;
                _this.wechat.interval = setInterval(function(){
                    _this.wechatState(_this.wechat.state,_this.wechat.mode);
                },3000)
            },
            wechat_close(){
                if( this.wechat.interval ){
                    clearInterval(this.wechat.interval);
                }
                this.wechat.wechat_visible = false;
            },
            async bindWechat(){
                this.wechat.qrcode = '';
                this.wechat.state = '';

                let res = await this.wechatState();

                if( res ){
                    this.wechat.wechat_visible = true;
                    this.wechat_start();
                }
            },
            unBindWechat(){
                this.$confirm('确认解绑微信？')
                    .then(async () => {
                        let res = await unbind_wechat();
                        let msg_type = 'error';
                        if( res.data.code == 1 ){
                            this.$store.commit('user/SET_WECHAT_STATUS', false);
                            msg_type = 'success';
                        }
                        this.$message( res.data.msg ,msg_type);
                    })
                    .catch( (e) => {
                        console.log(e);
                    });
            },
            bindGoogle(){},
        },
        beforeDestroy() {
            this.wechat_close();
        }
    }
</script>

<style lang="scss" scoped>
    .navbar {
        height: 50px;
        overflow: hidden;
        position: relative;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0,21,41,.08);

        .hamburger-container {
            line-height: 46px;
            height: 100%;
            float: left;
            cursor: pointer;
            transition: background .3s;
            -webkit-tap-highlight-color:transparent;

            &:hover {
                background: rgba(0, 0, 0, .025)
            }
        }

        .breadcrumb-container {
            float: left;
        }

        .errLog-container {
            display: inline-block;
            vertical-align: top;
        }

        .right-menu {
            float: right;
            height: 100%;
            line-height: 50px;

            &:focus {
                outline: none;
            }

            .right-menu-item {
                display: inline-block;
                padding: 0 8px;
                height: 100%;
                font-size: 18px;
                color: #5a5e66;
                vertical-align: text-bottom;

                &.hover-effect {
                    cursor: pointer;
                    transition: background .3s;

                    &:hover {
                        background: rgba(0, 0, 0, .025)
                    }
                }
            }

            .avatar-container {
                margin-right: 30px;

                .avatar-wrapper {
                    margin-top: 5px;
                    position: relative;

                    .user-avatar {
                        cursor: pointer;
                        width: 40px;
                        height: 40px;
                        border-radius: 10px;
                    }

                    .el-icon-caret-bottom {
                        cursor: pointer;
                        position: absolute;
                        right: -20px;
                        top: 25px;
                        font-size: 12px;
                    }
                }
            }
        }
    }
</style>
