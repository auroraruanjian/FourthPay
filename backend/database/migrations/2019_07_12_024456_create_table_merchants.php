<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMerchants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 商户表
         */
        Schema::create('merchants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account',60)->comment('商户号');
            $table->string('password')->comment('密码');
            $table->text('system_public_key','')->comment('系统公钥');
            $table->text('system_private_key','')->comment('系统私钥');
            $table->text('merchant_public_key','')->comment('商户公钥');
            $table->text('merchant_private_key','')->comment('商户私钥');
            $table->string('md5_key','')->comment('MD5签名校验秘钥');

            $table->tinyInteger('status')->default(0)->comment('状态:0 正常，1 冻结');

            $table->rememberToken();
            $table->timestamps();
        });

        $this->_permission();
    }

    private function _permission()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => 0,
            'rule'        => 'member',
            'name'        => '会员管理',
            'extra'       => json_encode(['icon' => 'list','component'=>'Layout']),
        ]);

        $merchant_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => $id,
            'rule'        => 'merchant/index',
            'name'        => '商户列表',
            'extra'       => json_encode(['icon' => 'client','component'=>'merchant/index']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $merchant_id,
                'rule'        => 'merchant/create',
                'name'        => '增加商户',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $merchant_id,
                'rule'        => 'merchant/edit',
                'name'        => '编辑商户',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $merchant_id,
                'rule'        => 'merchant/delete',
                'name'        => '删除商户',
                'extra'       => json_encode(['hidden' => true]),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}
