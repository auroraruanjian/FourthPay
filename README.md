# Laravel System
> 使用Laravel+Vue构建的一个集成前后台的项目，包含开箱即用的权限管理模块，日志管理模块，微信登录，系统配置，用户管理等功能

技术栈
----

#### 后端技术栈：

* Laravel       5.8
* Laravel-echo-server
* Nginx
* PostgreSql 或 Mysql
* Laravel-mix
* Redis
* PHP APC

#### 前端技术栈

* Vue      
* Vuex
* Vue-router
* Element UI

> 后台模板采用：[Element-Admin](https://github.com/PanJiaChen/vue-admin-template/)   后台演示：[Laravel后台管理系统](http://www.tuo0.com)  
> 前台模板采用VUE CLI3构建，采用Element UI作为开发前端开发框架

目录结构
---------
* backend       ：管理后台
* frontend-api  ：前台API接口目录
* frontend-web  ：前台前端文件
* common        ：公共模块目录
* doc           ：相关文档和配置目录

> 前台采用前后端分离，frontend-api需要配置允许跨域访问

部署方法
-------
#### 1.安装Nginx、Redis、Mysql或PostgreSql、Laravel-echo-server、composer、nodejs服务，安装PHP以及所需扩展，拷贝doc目录下配置文件到nginx配置目录
#### 2.拉取代码，安装项目扩展库
```bash
$ git clone git@github.com:tuo0/laravel-admin.git
$ cd laravel-admin/backend/
$ composer install
$ npm install
$ cd ../frontend-api
$ composer install
$ cd ../frontend-web
$ npm install
```

#### 3.启动对接监听
```bash
$ php artisan queue:work redis
```

#### 4.安装部署laravel-echo
```bash
# 安装
$ npm install -g laravel-echo-server
# 配置
$ laravel-echo-server init
# 启动
$ laravel-echo-server start
```

#### 5.启动redis订阅队列
```bash
$ php artisan queue:listen --tries=1
```

#### 6.复制 .env.example 文件为 .env,修改数据库、Redis，创建key
```bash
$ php artisan  key:generate
```

#### 7.编译前后台前端文件

##### 后台backend编译
```bash
$ npm run prod
```

##### 前台frontend-web编译
```bash
$ vue run build
```

Ajax 接口返回json数据说明
-----------------------
```php
[
    'code'  => 1,
    'msg'   => '',
    'data'  => [],
]
```
* code:错误码  1：成功  0:失败 >1:失败错误码
* msg:错误消息
* data:返回数据 

预览
----
![sdd](https://raw.githubusercontent.com/tuo0/laravel-admin/master/doc/images/index.png)
