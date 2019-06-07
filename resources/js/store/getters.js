const getters = {
    username: state => state.user.username,
    sidebar: state => state.app.sidebar,
    avatar: state => state.user.avatar,
    visitedViews: state => state.tagsView.visitedViews,
    cachedViews: state => state.tagsView.cachedViews,
    permission_routes: state => state.permission.routes,
}

export default getters