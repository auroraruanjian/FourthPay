<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_category', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
            $table->jsonb('methods')->default('[]')->comment('支付方式');
            $table->jsonb('param')->default('[]')->comment('参数');
            $table->boolean('status')->default(0)->comment('是否启用');
        });

        $this->_permission();
    }

    private function _permission()
    {
        $row = DB::table('admin_role_permissions')->where('name', '支付接口管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            return;
        }

        $payment_channel_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => $row->id,
            'rule'        => 'payment_category/index',
            'name'        => '支付渠道管理',
            'extra'       => json_encode(['icon' => 'payment_method','component'=>'payment/category']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $payment_channel_id,
                'rule'        => 'payment_category/create',
                'name'        => '增加支付渠道',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $payment_channel_id,
                'rule'        => 'payment_category/edit',
                'name'        => '修改支付渠道',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $payment_channel_id,
                'rule'        => 'payment_category/delete',
                'name'        => '删除支付渠道',
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
        Schema::dropIfExists('payment_category');
    }
}
