<template>
    <div>
        <sticky :z-index="10" :class-name="'sub-navbar '+status">
            <el-button v-loading="loading" style="margin-left: 10px;" type="success" @click="submitForm">
                发布
            </el-button>
            <el-button v-loading="loading" type="warning" @click="draftForm">
                草稿
            </el-button>
        </sticky>

        <div class="app-container" v-loading="loading">

            <div class="container">
                <el-form ref="postForm" :model="postForm" :rules="rules" class="form-container">


                    <div class="createPost-main-container">
                        <el-row>

                            <el-col :span="24">
                                <el-form-item style="margin-bottom: 40px;" prop="title">
                                    <MDinput v-model="postForm.subject" :maxlength="100" name="name" required>
                                        公告标题
                                    </MDinput>
                                </el-form-item>

                                <div class="postInfo-container">
                                    <el-row>
                                        <el-col :span="7">
                                            <el-form-item label-width="120px" label="发布时间:" class="postInfo-container-item">
                                                <el-date-picker v-model="published_at" type="datetime" format="yyyy-MM-dd HH:mm:ss" placeholder="Select date and time" />
                                            </el-form-item>
                                        </el-col>

                                        <el-col :span="7">
                                            <el-form-item label-width="120px" label="排序:" class="postInfo-container-item">
                                                <el-input v-model="postForm.sort"></el-input>
                                            </el-form-item>
                                        </el-col>

                                        <el-col :span="7">
                                            <el-form-item label-width="120px" label="标记:" class="postInfo-container-item">
                                                <el-checkbox label="显示" name="type" v-model="postForm.is_show"></el-checkbox>
                                                <el-checkbox label="置顶" name="type" v-model="postForm.is_top"></el-checkbox>
                                                <el-checkbox label="弹出" name="type" v-model="postForm.is_alert"></el-checkbox>
                                            </el-form-item>
                                        </el-col>
                                    </el-row>
                                </div>
                            </el-col>
                        </el-row>

                        <el-form-item prop="content" style="margin-bottom: 30px;">
                            <Tinymce ref="editor" v-model="postForm.content" :height="400" />
                        </el-form-item>
                    </div>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
    import Tinymce from '@/components/Tinymce'
    import MDinput from '@/components/MDinput'
    import Sticky from '@/components/Sticky' // 粘性header组件
    import { postCreate, getEdit, putEdit } from '@/api/notice'

    const defaultForm = {
        id:'',
        subject : '',
        published_at : '',
        sort : 0,
        is_show : false,
        is_top : false,
        is_alert : false,
        content:'',
    }

    export default {
        name: "notices_form",
        components: { Tinymce, MDinput , Sticky },
        props: {
            isEdit: {
                type: Boolean,
                default: false
            }
        },
        data(){
            const validateRequire = (rule, value, callback) => {
                if (value === '') {
                    this.$message({
                        message: rule.field + '为必传项',
                        type: 'error'
                    })
                    callback(new Error(rule.field + '为必传项'))
                } else {
                    callback()
                }
            }

            return {
                loading:false,
                postForm: {},
                rules: {
                    title: [{ validator: validateRequire }],
                    content: [{ validator: validateRequire }],
                },
                status:'draft',
                tempRoute: {}
            };
        },
        computed: {
            published_at: {
                get() {
                    return (+new Date(this.postForm.published_at))
                },
                set(val) {
                    this.postForm.published_at = new Date(val)
                }
            }
        },
        created() {
            if (this.isEdit) {
                const id = this.$route.params && this.$route.params.id
                this.fetchData(id)
            } else {
                this.postForm = Object.assign({}, defaultForm)
            }

            // Why need to make a copy of this.$route here?
            // Because if you enter this page and quickly switch tag, may be in the execution of the setTagsViewTitle function, this.$route is no longer pointing to the current page
            // https://github.com/PanJiaChen/vue-element-admin/issues/1221
            this.tempRoute = Object.assign({}, this.$route)
        },
        methods:{
            fetchData(id) {
                getEdit( id ).then( response => {
                    this.postForm = response.data.data

                    // just for test
                    this.postForm.title += `   Article Id:${this.postForm.id}`
                    this.postForm.content_short += `   Article Id:${this.postForm.id}`

                    // set tagsview title
                    this.setTagsViewTitle()

                    // set page title
                    this.setPageTitle()
                }).catch(err => {
                    console.log(err)
                });
            },
            setTagsViewTitle() {
                const title = 'Edit Article'
                const route = Object.assign({}, this.tempRoute, { title: `${title}-${this.postForm.id}` })
                this.$store.dispatch('tagsView/updateVisitedView', route)
            },
            setPageTitle() {
                const title = 'Edit Article'
                document.title = `${title} - ${this.postForm.id}`
            },
            submitForm(){
                if( this.isEdit ){
                    putEdit( this.postForm ).then( response => {
                        let type = 'error';
                        if( response.data.code == 1 ){
                            type = 'success';
                        }

                        this.$message({
                            message: response.data.msg,
                            type
                        });
                    }).catch( err => {
                        console.log( err );
                    })
                }else{
                    postCreate( this.postForm ).then( response => {
                        let type = 'error';
                        if( response.data.code == 1 ){
                            type = 'success';
                        }

                        this.$message({
                            message: response.data.msg,
                            type
                        });
                    }).catch( err => {
                        console.log( err );
                    })
                }
            },
            draftForm(){

            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "res/sass/mixin.scss";

    .createPost-container {
        position: relative;

        .createPost-main-container {
            padding: 40px 45px 20px 50px;

            .postInfo-container {
                position: relative;
                @include clearfix;
                margin-bottom: 10px;

                .postInfo-container-item {
                    float: left;
                }
            }
        }

        .word-counter {
            width: 40px;
            position: absolute;
            right: 10px;
            top: 0px;
        }
    }

    .article-textarea /deep/ {
        textarea {
            padding-right: 40px;
            resize: none;
            border: none;
            border-radius: 0px;
            border-bottom: 1px solid #bfcbd9;
        }
    }
</style>