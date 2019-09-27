const getters = {
    username: state => state.user.username,
    sidebar: state => state.app.sidebar,
    avatar: state => state.user.avatar,
    visitedViews: state => state.tagsView.visitedViews,
    cachedViews: state => state.tagsView.cachedViews,
    permission_routes: state => state.permission.routes,
    permission_asyncRoutes: state => state.permission.addRoutes,
    user_permission: state => state.permission.permission,
}

export default getters