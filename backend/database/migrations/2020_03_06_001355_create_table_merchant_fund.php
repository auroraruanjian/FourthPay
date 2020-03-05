<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMerchantFund extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_fund', function (Blueprint $table) {
            $table->integer('merchant_id')->comment('商户ID');
            $table->decimal('balance', 14, 4)->default(0)->comment('帐户余额(可用+冻结)');
            $table->decimal('hold_balance', 14, 4)->default(0)->comment('冻结金额');
            $table->primary('merchant_id');
            $table->index(['merchant_id', 'balance']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_fund');
    }
}
