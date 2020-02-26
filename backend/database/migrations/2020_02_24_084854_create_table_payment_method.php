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
            $table->smallIncrements('id');
            $table->string('ident', 16)->unique()->comment('英文标识');
            $table->string('name', 32)->comment('中文名称');
            $table->boolean('status')->default(0)->comment('是否启用');
        });

        $this->_permission();
        $this->_data();
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

    private function _data()
    {
        DB::table('payment_method')->insert([
            [
                'ident'      => 'netbank',
                'name'       => '在线网银',
                'status'     => true,
            ],
            [
                'ident'      => 'wechat_scan',
                'name'       => '微信扫码',
                'status'     => true,
            ],
            [
                'ident'      => 'alipay_scan',
                'name'       => '支付宝扫码',
                'status'     => true,
            ],
            [
                'ident'      => 'qq_scan',
                'name'       => 'QQ扫码',
                'status'     => true,
            ],
            [
                'ident'      => 'jd_scan',
                'name'       => '京东扫码',
                'status'     => true,
            ],
            [
                'ident'      => 'unionpay_scan',
                'name'       => '银联扫码',
                'status'     => true,
            ],
            [
                'ident'      => 'wechat_h5',
                'name'       => '微信H5',
                'status'     => true,
            ],
            [
                'ident'      => 'alipay_h5',
                'name'       => '支付宝H5',
                'status'     => true,
            ],
            [
                'ident'      => 'qq_h5',
                'name'       => 'QQH5',
                'status'     => true,
            ],
            [
                'ident'      => 'jd_h5',
                'name'       => '京东H5',
                'status'     => true,
            ],
            [
                'ident'      => 'unionpay_h5',
                'name'       => '银联H5',
                'status'     => true,
            ],
            [
                'ident'      => 'quick',
                'name'       => '快捷支付',
                'status'     => true,
            ],
            [
                'ident'      => 'credit',
                'name'       => '信用卡',
                'status'     => true,
            ],
            [
                'ident'      => 'cashier',
                'name'       => '收银台',
                'status'     => true,
            ],
            [
                'ident'      => 'qrcode_offline',
                'name'       => '线下扫码',
                'status'     => true,
            ],
            [
                'ident'      => 'third_offline',
                'name'       => '第三方线下转账',
                'status'     => true,
            ],
            [
                'ident'      => 'digital_currency',
                'name'       => '数字货币',
                'status'     => true,
            ]
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
