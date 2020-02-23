<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
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
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account',60)->comment('商户号');
            $table->tinyInteger('status')->default(1)->comment('状态 1:正常 2:禁用');

            $table->timestamps();
        });

        $this->_permission();
    }

    private function _permission()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => 0,
            'rule'        => 'merchant',
            'name'        => '会员管理',
            'extra'       => json_encode(['icon' => 'list','component'=>'Layout']),
        ]);

        $merchant_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => $id,
            'rule'        => 'client/index',
            'name'        => '商户列表',
            'extra'       => json_encode(['icon' => 'client','component'=>'client/index']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $merchant_id,
                'rule'        => 'client/create',
                'name'        => '增加商户',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $merchant_id,
                'rule'        => 'client/edit',
                'name'        => '编辑商户',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $merchant_id,
                'rule'        => 'client/delete',
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
        Schema::dropIfExists('clients');
    }
}
