<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channel', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 40)->unique()->comment('名称');
            $table->smallInteger('payment_category_id')->comment('支付渠道ID');
            $table->jsonb('channel_param')->default('[]')->comment('支付通道参数');
//            $table->jsonb('methods_param')->default('[]')->comment('支付类型参数');
            $table->boolean('status')->default(0)->comment('是否启用');
            $table->timestamps();
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
            'rule'        => 'payment_channel/index',
            'name'        => '支付通道管理',
            'extra'       => json_encode(['icon' => 'payment_method','component'=>'payment/channel']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $payment_channel_id,
                'rule'        => 'payment_channel/create',
                'name'        => '增加支付通道',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $payment_channel_id,
                'rule'        => 'payment_channel/edit',
                'name'        => '修改支付通道',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $payment_channel_id,
                'rule'        => 'payment_channel/delete',
                'name'        => '删除支付通道',
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
        Schema::dropIfExists('payment_channel');
    }
}
