<template>
    <div class="app-container" v-loading="loading">
        <div class="container">
            <div class="handle-box">
                <el-button type="primary" icon="el-icon-circle-plus-outline" v-permission="'role/create'" @click="handleAddRole" size="small">新增管理员</el-button>
            </div>

            <el-table :data="rolesList" style="width: 100%;margin-top:30px;" border>
                <el-table-column align="center" label="Role Key" width="220">
                    <template slot-scope="scope">
                        {{ scope.row.id }}
                    </template>
                </el-table-column>
                <el-table-column align="center" label="Role Name" width="220">
                    <template slot-scope="scope">
                        {{ scope.row.name }}
                    </template>
                </el-table-column>
                <el-table-column align="header-center" label="Description">
                    <template slot-scope="scope">
                        {{ scope.row.description }}
                    </template>
                </el-table-column>
                <el-table-column align="center" label="Operations">
                    <template slot-scope="scope">
                        <el-button type="primary" size="small" @click="handleEdit(scope)" v-permission="'role/edit'">Edit</el-button>
                        <el-button type="danger" size="small" @click="handleDelete(scope)" v-permission="'role/delete'">Delete</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </div>

        <el-dialog :visible.sync="dialogVisible" width="580px" :title="dialogType==='edit'?'Edit Role':'New Role'">
            <el-form :model="role" label-width="15%" label-position="left">
                <el-form-item label="角色名">
                    <el-input v-model="role.name" placeholder="Role Name" />
                </el-form-item>
                <el-form-item label="描述">
                    <el-input
                            v-model="role.description"
                            :autosize="{ minRows: 2, maxRows: 4}"
                            type="textarea"
                            placeholder="Role Description"
                    />
                </el-form-item>
                <el-form-item label="权限">
                    <el-tree
                            ref="tree"
                            :check-strictly="checkStrictly"
                            :data="routesData"
                            :props="defaultProps"
                            show-checkbox
                            node-key="id"
                            class="permission-tree"
                    />
                </el-form-item>
            </el-form>
            <div style="text-align:right;">
                <el-button type="danger" @click="dialogVisible=false">Cancel</el-button>
                <el-button type="primary" @click="confirmRole">Confirm</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    import path from 'path'
    import permission from '@/directive/permission/index.js' // 权限判断指令
    // import { deepClone } from '@/utils'
    // import { getRoutes, getRoles, addRole, deleteRole, updateRole } from '@/api/role'
    import { createRole,getAllRoles,getRole,editRole } from '@/api/role'
    import { createRouter } from '@/utils/';
    // import { mapGetters } from 'vuex'

    const defaultRole = {
        id:'',
        key: '',
        name: '',
        description: '',
        routes: []
    }

    export default {
        data() {
            return {
                role: Object.assign({}, defaultRole),
                routes: [],
                rolesList: [],
                dialogVisible: false,
                dialogType: 'new',
                checkStrictly: true,
                defaultProps: {
                    children: 'children',
                    label: 'title'
                },
                loading:false,
            }
        },
        directives: { permission },
        computed: {
            routesData() {
                return this.routes
            },
            // ...mapGetters([
            //     'permission_asyncRoutes',
            // ]),
        },
        created() {
            this.getRoles()
        },
        methods: {
            async getRoles() {
                this.loading = true;
                const res = await getAllRoles()
                if( res.data.code == 1 ){
                    this.rolesList = res.data.data.roles;
                    let formatRouter = createRouter(res.data.data.permission);
                    this.routes = this.generateRoutes(formatRouter.asyncRouter);
                }else{
                    this.$message.error(res.data.msg);
                }
                this.loading = false;
            },
            // Reshape the routes structure so that it looks the same as the sidebar
            generateRoutes(routes, basePath = '/') {
                const res = []

                for (let route of routes) {
                    // skip some route
                    //if (route.hidden) { continue }

                    const data = {
                        id:route.meta.id,
                        path: path.resolve(basePath, route.path),
                        title: route.meta && route.meta.title
                    }

                    // recursive child routes
                    if (route.children) {
                        data.children = this.generateRoutes(route.children, data.path)
                    }
                    res.push(data)
                }
                return res
            },
            /*
            generateArr
            (routes) {
                let data = []
                routes.forEach(route => {
                    data.push(route)
                    if (route.children) {
                        const temp = this.generateArr(route.children)
                        if (temp.length > 0) {
                            data = [...data, ...temp]
                        }
                    }
                })
                return data
            },
            */
            handleAddRole() {
                this.role = Object.assign({}, defaultRole)
                if (this.$refs.tree) {
                    this.$refs.tree.setCheckedNodes([])
                }
                this.dialogType = 'new'
                this.dialogVisible = true
            },
            async handleEdit(scope) {
                let current_role = await getRole(scope.row.id);
                this.role = {
                    id:current_role.data.data.id,
                    key: current_role.data.data.key,
                    name: current_role.data.data.name,
                    description: current_role.data.data.description,
                    routes: current_role.data.data.routes
                };
                this.dialogType = 'edit'
                this.dialogVisible = true

                this.$nextTick(() => {
                    this.$refs.tree.setCheckedKeys(current_role.data.data.permission)
                })
            },
            handleDelete({ $index, row }) {
                this.$confirm('Confirm to remove the role?', 'Warning', {
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    type: 'warning'
                })
                    .then(async() => {
                        await deleteRole(row.key)
                        this.rolesList.splice($index, 1)
                        this.$message({
                            type: 'success',
                            message: 'Delete succed!'
                        })
                    })
                    .catch(err => { console.error(err) })
            },
            /*
            generateTree(routes, basePath = '/', checkedKeys) {
                const res = []

                for (const route of routes) {
                    const routePath = path.resolve(basePath, route.path)

                    // recursive child routes
                    if (route.children) {
                        route.children = this.generateTree(route.children, routePath, checkedKeys)
                    }

                    if (checkedKeys.includes(routePath) || (route.children && route.children.length >= 1)) {
                        res.push(route)
                    }
                }
                return res
            },
            */
            async confirmRole() {
                const isEdit = this.dialogType === 'edit'

                let type = 'error';
                let message = '';

                this.role.routes = this.$refs.tree.getCheckedKeys()

                let response;

                if (isEdit) {
                    response =  await editRole(this.role);
                }else{
                    response = await createRole(this.role);
                }

                if( response.data.code == 1 ){
                    type = 'success';
                    message = `
                            <div>Role Nmae: ${this.role.name}</div>
                            <div>Description: ${this.role.description}</div>
                          `;
                }else{
                    message = response.data.msg;
                }

                this.dialogVisible = false

                this.getRoles();

                this.$notify({
                    title: type=='success'?'Success':'Error',
                    dangerouslyUseHTMLString: true,
                    message: message,
                    type: type
                })
            },
            // reference: src/view/layout/components/Sidebar/SidebarItem.vue
            onlyOneShowingChild(children = [], parent) {
                let onlyOneChild = null
                const showingChildren = children.filter(item => !item.hidden)

                // When there is only one child route, the child route is displayed by default
                if (showingChildren.length === 1) {
                    onlyOneChild = showingChildren[0]
                    onlyOneChild.path = path.resolve(parent.path, onlyOneChild.path)
                    return onlyOneChild
                }

                // Show parent if there are no child route to display
                if (showingChildren.length === 0) {
                    onlyOneChild = { ... parent, path: '', noShowingChildren: true }
                    return onlyOneChild
                }

                return false
            }
        }
    }
</script>

<style lang="scss" scoped>
    .app-container {
        .roles-table {
            margin-top: 30px;
        }
        .permission-tree {
            margin-bottom: 30px;
        }
    }
</style>
