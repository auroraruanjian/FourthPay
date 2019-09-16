<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" @click="handleAddConfig" v-permission="'config/create'" size="small">创建配置</el-button>
                <el-link type="primary" @click.native="parent_id=0" v-if="parent_id > 0 " size="small">返回上一级</el-link>
            </div>

            <aside>上次刷新信息：{{last_refresh.username}}|{{last_refresh.time}}</aside>

            <el-table :data="config_list" style="width: 100%;margin-top:30px;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="center" label="配置标题" >
                    <template slot-scope="scope" >
                        <el-tooltip class="item" effect="dark" :content="scope.row.description||scope.row.title" placement="top">
                            <el-link type="primary" v-if="scope.row.parent_id==0" @click.native="parent_id = scope.row.id">{{scope.row.title}}</el-link>
                            <el-link type="info" v-else :underline="false">{{scope.row.title}}</el-link>
                        </el-tooltip>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="配置名称" prop="key"></el-table-column>
                <el-table-column align="header-center" label="配置值">
                    <template slot-scope="scope">
                        <el-select
                                v-if="scope.row.type==2"
                                v-model="scope.row.value">
                            <el-option v-for="(item,key) in scope.row.extra.data"
                               :key="key"
                               :label="item.key"
                               :value="item.value">
                            </el-option>
                        </el-select>
                        <el-switch
                                v-else-if="scope.row.type==3"
                                v-model="scope.row.value"
                                active-color="#13ce66"
                                inactive-color="#ddd">
                        </el-switch>
                        <span v-else>{{scope.row.value}}</span>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="状态" width="100">
                    <template slot-scope="scope">
                        <el-tag type="success" v-if="scope.row.is_disabled">正常</el-tag>
                        <el-tag type="danger" v-else>禁用</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope" >
                        <el-link type="primary" icon="el-icon-setting" @click.native="handleEdit(scope,'setting')">设置</el-link>
                        <el-link type="primary" icon="el-icon-edit" @click.native="handleEdit(scope,'edit')">编辑</el-link>
                        <el-link type="danger" icon="el-icon-delete" @click.native="handleDelete(scope)">删除</el-link>
                    </template>
                </el-table-column>
            </el-table>

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getAllConfig" />
        </div>

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'Edit Config':(dialogType=='setting')?'Setting Config':'New Config'">
            <el-form :model="config" label-width="15%" label-position="right">
                <el-form-item label="上级菜单">
                    <el-select v-model="config.parent_id" placeholder="请选择" :disabled="(dialogType=='edit')?true:false">
                        <el-option
                                v-for="item in parents_config"
                                :key="item.id"
                                :label="item.title"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="配置标题">
                    <el-input v-model="config.title" placeholder="配置标题" :disabled="(dialogType=='edit')?true:false"/>
                </el-form-item>
                <el-form-item label="系统配置名称">
                    <el-input v-model="config.key" placeholder="系统配置名称" :disabled="(dialogType=='edit')?true:false"/>
                </el-form-item>
                <el-form-item label="系统配置类型" v-if="dialogType=='setting'">
                    <el-select v-model="config.type" placeholder="系统配置类型" >
                        <el-option label="输入框" value="1"></el-option>
                        <el-option label="下拉框" value="2"></el-option>
                        <el-option label="开关"   value="3"></el-option>
                        <el-option label="单选框" value="4"></el-option>
                        <el-option label="多选框" value="5"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="扩展数据" v-if="dialogType=='setting'">
                    <el-input v-model="config.extra" placeholder='值列表，1.开关:0-关闭 1-开启 ,2.下拉框、单选、多选:["data":[{key:"选项",value:"值"}]] 3.输入框:{"encrypt":true}' />
                </el-form-item>
                <el-form-item label="系统配置值">
                    <el-select
                            v-if="config.type==2"
                            v-model="config.value">
                        <el-option v-for="(item,key) in config.extra_docode.data"
                                   :key="key"
                                   :label="item.key"
                                   :value="item.value">
                        </el-option>
                    </el-select>
                    <el-switch
                            v-else-if="config.type==3"
                            v-model="config.value"
                            active-color="#13ce66"
                            inactive-color="#ddd">
                    </el-switch>
                    <el-radio-group v-else-if="config.type==4" v-model="config.value">
                        <el-radio-button v-for="(item,key) in config.extra_docode.data" :key="key" :label="item.value">{{item.key}}</el-radio-button>
                    </el-radio-group>
                    <el-input v-else v-model="config.value" placeholder="系统配置值" />
                </el-form-item>
                <el-form-item label="是否启用">
                    <el-switch
                            v-model="config.is_disabled"
                            active-color="#13ce66"
                            inactive-color="#ddd">
                    </el-switch>
                </el-form-item>
                <el-form-item label="系统描述">
                    <el-input
                            type="textarea"
                            :autosize="{ minRows: 2, maxRows: 4}"
                            placeholder="请输入内容"
                            :disabled="(dialogType=='edit')?true:false"
                            v-model="config.description">
                    </el-input>
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
    import { getAllConfig,settingConfig,editConfig,addConfig,getConfig,deleteConfig } from '@/api/config'
    import { mapGetters } from 'vuex'


    const defaultConfig = {
        id:'',
        parent_id:'',
        title:'',
        key:'',
        value:'',
        type:1,
        extra:'',
        extra_docode:[],
        description:'',
        is_disabled:true,
    };

    export default {
        name: "config",
        data(){
            return {
                config: Object.assign({}, defaultConfig),
                config_list: [],
                parent_id:0,
                parents_config:[],
                total: 0,
                listQuery: {
                    page: 1,
                    limit: 20
                },
                dialogVisible: false,
                dialogType: 'new',
                loading:false,
                last_refresh:{
                    username:'',
                    time:'',
                },
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
            this.getAllConfig();
            let _this = this;

            //window.Echo.leave('message.'+this.username);
            window.Echo.private('message.'+this.username)
                .listen('NotifyEvent', (e) => {
                    console.log(e);
                    _this.$message.success('用户：'+e.data.username+'，刷新时间：'+e.data.time);
                });
        },
        methods:{
            async getAllConfig(){
                this.loading =  true;

                let data = this.listQuery;
                data.parent_id = this.parent_id;
                let result = await getAllConfig(data);

                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.config_list = result.data.data.config_list;
                    this.parents_config = result.data.data.top_config;
                    if( result.data.data.last_refresh != null ) this.last_refresh = result.data.data.last_refresh;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleAddConfig(){
                this.config = Object.assign({}, defaultConfig)
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit( scope , mode ){
                this.loading =  true;
                let current_config = await getConfig(scope.row.id);
                this.config = current_config.data.data;
                this.config.extra_docode = JSON.parse( this.config.extra );
                this.dialogType = mode
                this.dialogVisible = true
                this.loading =  false;
            },
            handleDelete( scope ){
                this.$confirm('此操作将永久删除该配置, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then( async() => {
                    let result = await deleteConfig( scope.row.id )
                    if( result.data.code == 1 ){
                        this.$message({
                            type: 'success',
                            message: '删除成功!'
                        });
                        this.getAllConfig();
                    }else{
                        this.$message.error(result.data.message);
                    }
                }).catch((e) => {
                    console.log(e);
                });
            },
            async confirm(){
                let type = 'error';
                let message = '';

                let response;

                if(this.dialogType == 'setting'){
                    response = await settingConfig(this.config)
                }else if (this.dialogType == 'edit') {
                    response = await editConfig(this.config)
                }else{
                    response = await addConfig(this.config)
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>Config name: ${this.config.title}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getAllConfig();

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
                this.getAllConfig();
            }
        }
    }
</script>

<style scoped>

</style>