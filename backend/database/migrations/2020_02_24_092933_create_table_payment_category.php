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
            $table->jsonb('method_idents')->default('{}')->comment('支付方式');
            $table->jsonb('param')->default('[]')->comment('参数');
            $table->boolean('status')->default(0)->comment('是否启用');
        });

        $this->_permission();
        $this->_data();
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

    private function _data()
    {
        DB::table('payment_category')->insert([
            [
                'ident'         => 'wechat',
                'name'          => '微信',
                'method_idents' => json_encode(['wechat_scan','wechat_h5']),
                'param'         => json_encode([
                    [
                        'name'          => '公众账号ID',
                        'ident'         => 'appid',
                        'type'          => '1',  // 0.文本 1.下拉
                        'default_value' => '',
                        'require'       => true, // 是否为必须参数
                    ],
                    [
                        'name'          => '商户号',
                        'ident'         => 'merchant_id',
                        'type'          => '1', // 0.文本 1.下拉
                        'default_value' => '',
                        'require'       => true, // 是否为必须参数
                    ],
                    [
                        'name'          => '商户密钥',
                        'ident'         => 'key1',
                        'type'          => '1', // 0.文本 1.下拉
                        'default_value' => '',
                        'require'       => true, // 是否为必须参数
                    ],
                ]),
                'status'        => true,
            ],
            [
                'ident'         => 'alipay',
                'name'          => '支付宝',
                'method_idents' => json_encode(['alipay_scan','alipay_h5']),
                'param'         => json_encode([
                    [
                        'name'          => '应用ID',
                        'ident'         => 'appid',
                        'type'          => '1',  // 0.文本 1.下拉
                        'default_value' => '',
                        'require'       => true, // 是否为必须参数
                    ],
                    [
                        'name'          => '商户私钥',
                        'ident'         => 'key1',
                        'type'          => '1', // 0.文本 1.下拉
                        'default_value' => '',
                        'require'       => true, // 是否为必须参数
                    ],
                    [
                        'name'          => '支付宝公钥',
                        'ident'         => 'key2',
                        'type'          => '1', // 0.文本 1.下拉
                        'default_value' => '',
                        'require'       => true, // 是否为必须参数
                    ],
                ]),
                'status'        => true,
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
