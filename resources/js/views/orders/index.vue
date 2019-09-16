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

                        <el-form-item label="IP地址">
                            <el-input v-model="search.id"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row justify="center" type="flex">
                    <el-button type="primary" icon="el-icon-search" @click="getOrders" size="small">搜索</el-button>
                    <el-button type="warning" icon="el-icon-circle-plus-outline" @click="handleExport" size="small">导出</el-button>
                </el-row>
            </el-form>
        </div>

        <div class="container" style="margin-top:20px;">
            <el-table :data="orders" style="width: 100%;" border >
                <el-table-column align="center" label="ID" prop="id"></el-table-column>
                <el-table-column align="header-center" label="账变时间" prop="created_at"></el-table-column>
                <el-table-column align="header-center" label="用户名" prop="from_user_id"></el-table-column>
                <el-table-column align="header-center" label="账变类型" prop="order_type_id"></el-table-column>
                <el-table-column align="header-center" label="支出" prop="amount">
                    <template slot-scope="scope">
                        <el-tag type="error" v-if="scope.row.amount<0">{{ scope.row.amount }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="收入">
                    <template slot-scope="scope">
                        <el-tag type="success" v-if="scope.row.amount>=0">{{ scope.row.amount }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="后余额/冻结">
                    <template slot-scope="scope">
                        {{ scope.row.pre_balance }}/{{ scope.row.pre_hold_balance }}
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="前余额/冻结">
                    <template slot-scope="scope">
                        {{ scope.row.balance }}/{{ scope.row.hold_balance }}
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="IP地址" prop="ip"></el-table-column>
                <el-table-column align="header-center" label="客户端" prop="client_type"></el-table-column>
                <el-table-column align="header-center" label="备注" prop="comment"></el-table-column>
                <el-table-column align="header-center" label="管理员" prop="admin_user_id"></el-table-column>
            </el-table>

            <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getOrders" />
        </div>
    </div>
</template>

<script>
    import permission from '@/directive/permission/index.js'    // 权限判断指令
    import Pagination from '@/components/Pagination'            // Secondary package based on el-pagination
    import { getOrders } from '@/api/orders'

    export default {
        name: "orders",
        data(){
            return {
                loading:false,
                search:{
                    id: '',
                    time : [new Date(2000, 10, 10, 10, 10), new Date(2000, 10, 11, 10, 10)],
                },
                user_group: [],
                admin_list: [],
                orders: [],
                total: 0,
                listQuery: {
                    page: 1,
                    limit: 20
                },
            };
        },
        computed: {
        },
        components: { Pagination },
        directives: { permission },
        created(){
            this.getOrders();
        },
        methods:{
            async getOrders(){
                this.loading =  true;

                let data = this.listQuery;
                data.parent_id = this.parent_id;
                let result = await getOrders(data);

                if( result.data.code == 1 ){
                    this.total = result.data.data.total;
                    this.orders = result.data.data.orders;
                }else{
                    this.$message.error(result.data.message);
                }
                this.loading =  false;
            },
            handleExport(){

            }
        },
    }
</script>

<style scoped>

</style>