<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePaymentChannelDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_channel_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('payment_channel_id')->unsigned()->comment('支付通道ID');
            $table->smallInteger('payment_method_id')->unsigned()->comment('支付渠道ID');
            $table->decimal('rate',15,4)->default(0)->comment('第三方费率(%),第三方收取平台的费率');
            $table->decimal('min_amount',15,4)->default(0)->comment('最低支付金额');
            $table->decimal('max_amount',15,4)->default(0)->comment('最高支付金额');
            $table->boolean('status')->default(0)->comment('是否启用');
            $table->jsonb('top_user_ids')->default('{}')->comment('开通的总代列表');
            $table->string('start_time')->default('00:00:00')->comment('每天开始时间');
            $table->string('end_time')->default('00:00:00')->comment('每天开始时间,如果结束时间小于开始时间，则表示跨天');
            $table->jsonb('extra')->default('{}')->comment('扩展参数');
            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_channel_detail');
    }
}
