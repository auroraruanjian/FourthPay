<?php

namespace Common\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    //
    protected $table='payment_channel';

    /**
     * 获取支付通道详情
     */
    public function detail()
    {
        return $this->hasMany('Common\Models\PaymentChannelDetail','payment_channel_id','id');
    }
}
