<?php

namespace Common\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    //
    protected $table='payment_channel';

    /**
     * 获取博客文章的评论
     */
    public function detail()
    {
        return $this->hasMany('Common\Models\PaymentChannelDetail','payment_channel_id','id');
    }
}
