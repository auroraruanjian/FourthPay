<?php

namespace Common\API\Server\Wechat;

use Common\API\Server\Base;
use Illuminate\Support\Str;

class Wechat extends Base
{

    public function query($data)
    {
        // TODO: Implement query() method.
    }

    public function callback($order)
    {
        $data = [
            'id'        => id_encode($order['id']),
            'order_no'  => Str::random(10),
            'amount'    => $order['amount'],
        ];

        return $data;
    }
}
