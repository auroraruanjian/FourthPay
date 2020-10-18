<?php

namespace Common\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentChannelDetail extends Model
{
    use SoftDeletes;

    //
    protected $table='payment_channel_detail';

    /**
     * 获取对应的支付通道
     */
    public function channel()
    {
        return $this->belongsTo('Common\Models\PaymentChannel','id','payment_channel_id');
    }
}
