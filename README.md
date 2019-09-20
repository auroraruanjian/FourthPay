## 一个用Laravel，Vue构建的后台程序

---

后端技术栈：
---------
* Laravel       5.8
* nginx
* postgreSql 或 Mysql
* laravel-mix
* Redis
* PHP APC

前端技术栈
---------
* Vue      
* Vuex
* Vue-router
* Element UI

模板采用：[Element-Admin](https://github.com/PanJiaChen/vue-admin-template/)
演示：[Laravel后台管理系统](http://www.tuo0.com)

目录结构
------
* backstage ：项目管理后台
* front     ：项目前台
* service   ：公共模块目录

部署方法
-------

#### 1.拉取代码，安装扩展库
```bash
$ git clone git@github.com:tuo0/laravel-admin.git
$ cd laravel-admin
$ composer install
$ npm install
```

#### 2.启动对接监听
```bash
$ php artisan queue:work redis
```

#### 3.安装部署laravel-echo
```bash
# 安装
$ npm install -g laravel-echo-server
# 配置
$ laravel-echo-server init
# 启动
$ laravel-echo-server start
```

#### 4.启动redis订阅队列
```bash
$ php artisan queue:listen --tries=1
```

#### 5.复制 .env.example 文件为 .env,修改数据库、Redis，创建key
```bash
$ php artisan  key:generate
```

#### 6.编译前端文件
```bash
$ npm run prod
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

![sdd](https://raw.githubusercontent.com/tuo0/laravel-admin/master/README/images/index.png)