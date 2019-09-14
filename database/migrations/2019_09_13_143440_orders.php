
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Orders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('游戏帐变ID');
            $table->integer('from_user_id')->default(0)->comment('(发起人)用户 ID');
            $table->integer('to_user_id')->default(0)->comment('(关联人)用户 ID');
            $table->smallInteger('admin_user_id')->default(0)->comment('管理员 ID');
            $table->smallInteger('order_type_id')->comment('帐变类型');
            $table->decimal('amount', 14, 4)->comment('本条账变所产生的资金变化量');
            $table->decimal('pre_balance', 14, 4)->comment('帐变前账户余额');
            $table->decimal('pre_hold_balance', 14, 4)->comment('帐变前冻结资金');
            $table->decimal('balance', 14, 4)->comment('帐变后账户余额');
            $table->decimal('hold_balance', 14, 4)->comment('帐变后冻结资金');
            $table->string('comment', 256)->default('')->comment('备注');
            $table->smallInteger('client_type')->default(0)->comment('客户端类型');
            $table->ipAddress('ip')->nullable()->comment('客户端 IP');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('账变时间');

            $table->index('created_at');
            $table->index(['ip', 'created_at']);
            $table->index(['admin_user_id', 'created_at']);
            $table->index(['order_type_id', 'created_at']);
            $table->index(['from_user_id', 'order_type_id', 'created_at']);
        });

        $this->_permission();
    }

    private function _permission()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => 0,
            'rule'        => 'orders',
            'name'        => '财务管理',
            'extra'       => json_encode(['icon' => 'list','component'=>'Layout']),
        ]);

        DB::table('admin_role_permissions')->insert([
            'parent_id'   => $id,
            'rule'        => 'orders/index',
            'name'        => '账变记录',
            'extra'       => json_encode(['icon' => 'users','component'=>'orders/index']),
        ]);

        $deposit_id = DB::table('admin_role_permissions')->insertGetID([
            'parent_id'   => $id,
            'rule'        => 'orders/index',
            'name'        => '充值记录',
            'extra'       => json_encode(['icon' => 'users','component'=>'orders/index']),
        ]);

        $withdrawal_id = DB::table('admin_role_permissions')->insertGetID([
            'parent_id'   => $id,
            'rule'        => 'withdrawal/index',
            'name'        => '提现记录',
            'extra'       => json_encode(['icon' => 'users','component'=>'orders/index']),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('orders');
    }
}
