## 一个用Laravel，Vue构建的后台程序

---

后端技术栈：
* Laravel       5.8
* nginx
* postgreSql 或 Mysql
* laravel-mix
* Redis
* PHP APC

前端技术栈
* Vue      
* Vuex
* Vue-router
* Element UI

模板采用：[Element-Admin](https://github.com/PanJiaChen/vue-admin-template/)

部署方法
```bash
拉取代码，安装扩展
$ git clone git@github.com:tuo0/p2p.git
$ cd p2p
$ composer install
$ npm install
```

队列监听
```bash
$ php artisan queue:work redis
```

laravel-echo
```bash
# 安装
$ npm install -g laravel-echo-server
# 配置
$ laravel-echo-server init
# 启动
$ laravel-echo-server start
```

redis订阅队列
```bash
$ php artisan queue:listen --tries=1
```

复制 .env.example 文件为 .env,修改数据库、Redis，创建key
```bash
$ php artisan  key:generate
```

编译前端文件
```bash
$ npm run prod
```

Ajax 接口返回json数据说明
```php
[
    'code'  => 1,
    'msg'   => '',
    'data'  => [],
]
```
* code:错误码  1：成功  0:失败 <0:失败错误码
* msg:错误消息
* data:返回数据 

