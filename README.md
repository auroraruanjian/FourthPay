## 一个用Laravel，Vue构建的后台程序

---

技术栈：
* Laravel       5.8
* nginx
* postgreSql
* laravel-mix
* Vue      
* Vuex
* Vue-router

部署方法
```bash
拉取代码，安装扩展
$ git clone git@github.com:tuo0/p2p.git
$ cd p2p
$ composer install
$ npm install
```

复制 .env.example 文件为 .env,修改数据库连接，创建key
```bash
$ php artisan  key:generate
```

Ajax json数据说明
```php
[
    'code'  => 1,
    'msg'   => '',
    'data'  => [],
]
```
code:错误码  1：成功  <1:失败 

