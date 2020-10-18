<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_type', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('类型 ID');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 30)->comment('中文名称');
            $table->tinyInteger('display')->default(0)->comment('是否显示给用户，0：不显示，1：显示）');
            $table->tinyInteger('operation')->default(0)->comment('账变对余额影响的操作, 0：无操作, 1：加钱，2：扣钱');
            $table->tinyInteger('hold_operation')->default(0)->comment('冻结金额影响的操作, 0：无操作, 1：加钱，2：扣钱');
            $table->smallInteger('category')->default(0)->comment('分类，1, 冲提:2, 三方：3');
            $table->string('description', 32)->default('')->comment('帐变类型描述');
        });

        $this->_data();
    }

    private function _data()
    {
        DB::table('order_type')->insert([
            [
                'name'           => '在线充值',
                'ident'          => 'ZXCZ',
                'display'        => 1,
                'operation'      => 1,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '在线支付充值',
            ],
            [
                'name'           => '理赔充值',
                'ident'          => 'LPCZ',
                'display'        => 1,
                'operation'      => 1,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '理赔充值',
            ],
            [
                'name'           => '促销充值',
                'ident'          => 'CXCZ',
                'display'        => 1,
                'operation'      => 1,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '促销充值',
            ],
            [
                'name'           => '人工充值',
                'ident'          => 'RGCZ',
                'display'        => 1,
                'operation'      => 1,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '人工充值',
            ],
            [
                'name'           => '充值手续费',
                'ident'          => 'CZSXF',
                'display'        => 1,
                'operation'      => 2,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '充值手续费',
            ],
            [
                'name'           => '返还手续费',
                'ident'          => 'FHSXF',
                'display'        => 1,
                'operation'      => 1,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '返还手续费',
            ],

            [
                'name'           => '提现申请',
                'ident'          => 'TKSQ',
                'display'        => 1,
                'operation'      => 2,
                'hold_operation' => 1,
                'category'       => 2,
                'd escription'   => '提现申请',
            ],
            [
                'name'           => '提款成功',
                'ident'          => 'TKCG',
                'display'        => 1,
                'operation'      => 0,
                'hold_operation' => 2,
                'category'       => 2,
                'description'    => '提款成功',
            ],
            [
                'name'           => '提款失败',
                'ident'          => 'TKSB',
                'display'        => 1,
                'operation'      => 1,
                'hold_operation' => 2,
                'category'       => 2,
                'description'    => '提款失败',
            ],
            [
                'name'           => '扣除提款手续费',
                'ident'          => 'TKSXF',
                'display'        => 1,
                'operation'      => 0,
                'hold_operation' => 2,
                'category'       => 2,
                'description'    => '扣除提款手费',
            ],
            [
                'name'           => '返还提款手续费',
                'ident'          => 'FHTKSXF',
                'display'        => 1,
                'operation'      => 1,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '返还提款手续费',
            ],
            [
                'name'           => '管理员减扣',
                'ident'          => 'GLYJK',
                'display'        => 1,
                'operation'      => 2,
                'hold_operation' => 0,
                'category'       => 2,
                'description'    => '管理员减扣',
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
        Schema::dropIfExists('order_type');
    }
}
