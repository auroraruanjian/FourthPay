<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_method', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
            $table->boolean('status')->default(0)->comment('是否启用');
        });

        $this->_permission();
    }

    private function _permission()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => 0,
            'rule'        => 'payment',
            'name'        => '支付接口管理',
            'extra'       => json_encode(['icon' => 'payment','component'=>'Layout']),
        ]);

        $payment_method_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => $id,
            'rule'        => 'payment_method/index',
            'name'        => '支付类型管理',
            'extra'       => json_encode(['icon' => 'payment_method','component'=>'payment/method']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $payment_method_id,
                'rule'        => 'payment_method/create',
                'name'        => '增加支付类型',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $payment_method_id,
                'rule'        => 'payment_method/edit',
                'name'        => '修改支付类型',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $payment_method_id,
                'rule'        => 'payment_method/delete',
                'name'        => '删除支付类型',
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
        Schema::dropIfExists('payment_method');
    }
}
