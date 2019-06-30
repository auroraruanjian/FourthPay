<template>
    <div class="app-container" v-loading="loading">
        <el-button type="primary" @click="handleAddConfig" v-permission="'config/create'">创建配置</el-button>
        <el-link type="primary" @click.native="parent_id=0" v-if="parent_id > 0 ">返回上一级</el-link>

        <p>上次刷新信息：{{last_refresh.username}}|{{last_refresh.time}}</p>

        <el-table :data="config_list" style="width: 100%;margin-top:30px;" border @cell-mouse-enter="handleHover" @cell-mouse-leave="handleLeave">
            <el-table-column align="center" label="ID" prop="id"></el-table-column>
            <el-table-column align="center" label="配置标题" >
                <template slot-scope="scope" >
                    <el-tooltip class="item" effect="dark" :content="scope.row.description" placement="top">
                        <el-link type="primary" @click.native="parent_id = scope.row.id">{{scope.row.title}}</el-link>
                    </el-tooltip>
                </template>
            </el-table-column>
            <el-table-column align="header-center" label="配置名称" prop="key"></el-table-column>
            <el-table-column align="header-center" label="配置值" prop="value"></el-table-column>
            <el-table-column align="header-center" label="状态" >
                <template slot-scope="scope">
                    <el-tag type="success" v-if="scope.row.is_disabled">正常</el-tag>
                    <el-tag type="danger" v-else>禁用</el-tag>
                </template>
            </el-table-column>
            <el-table-column align="center" label="Operations">
                <template slot-scope="scope" >
                    <el-button type="primary" size="small" @click="handleEdit(scope)">Edit</el-button>
                    <el-button type="danger" size="small" @click="handleDelete(scope)">Delete</el-button>
                </template>
            </el-table-column>
        </el-table>

        <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getAllConfig" />

        <el-dialog :visible.sync="dialogVisible" :title="dialogType==='edit'?'Edit Config':'New Config'">
            <el-form :model="config" label-width="15%" label-position="right">
                <el-form-item label="上级菜单">
                    <el-select v-model="config.parent_id" placeholder="请选择">
                        <el-option
                                v-for="item in parents_config"
                                :key="item.id"
                                :label="item.title"
                                :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="配置标题">
                    <el-input v-model="config.title" placeholder="配置标题" />
                </el-form-item>
                <el-form-item label="系统配置名称">
                    <el-input v-model="config.key" placeholder="系统配置名称" />
                </el-form-item>
                <el-form-item label="系统配置值">
                    <el-input v-model="config.value" placeholder="系统配置值" />
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
    import { getAllConfig,editConfig,addConfig,getConfig,deleteConfig } from '@/api/config'


    const defaultConfig = {
        id:'',
        parent_id:'',
        title:'',
        key:'',
        value:'',
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

        },
        components: { Pagination },
        directives: { permission },
        created() {
            this.getAllConfig();
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
            async handleEdit( scope ){
                this.loading =  true;
                let current_config = await getConfig(scope.row.id);
                this.config = current_config.data.data;
                this.dialogType = 'edit'
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
                const isEdit = this.dialogType === 'edit'

                let type = 'error';
                let message = '';

                let response;

                if (isEdit) {
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
            handleHover(row, column, cell, event){
                console.log(row, column, cell, event);

            },
            handleLeave(){

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