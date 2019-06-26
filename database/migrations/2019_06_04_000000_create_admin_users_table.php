<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nickname')->comment('用户昵称');
            $table->string('username')->unique()->comment('用户名');
            $table->string('password');
            $table->boolean('is_locked')->default(0)->comment('是否锁定');
            $table->string('unionid')->nullable()->comment('微信登陆唯一ID');
            $table->ipAddress('last_ip')->nullable()->comment('最后一次登录IP');
            $table->timestamp('last_time')->nullable()->comment('最后登录时间');
            $table->string('last_session', 64)->default('')->comment('最近登陆SESSIONID');
            $table->string('google_key', 16)->default('')->comment('谷歌登录器秘钥');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('admin_users')->insert([
            'nickname'  => '超级管理员',
            'username'  => 'admin',
            'password'  => Hash::make('admin123'),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
}
