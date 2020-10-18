<?php
namespace Common\API\Payment\Wechat;

use Common\API\Payment\Base;

class Wechat extends Base
{

    /**
     * 构建支付数据
     * @param $data
     * @return Array
     */
    public function prepare_pay($data)
    {
        // TODO: Implement prepare_pay() method.
        return [
            PAY_VIEW_TYPE_HTML,
            'Success',
            []
        ];
    }

    /**
     * 构建订单查询数据
     * @param $data
     * @return mixed
     */
    public function prepare_query($data)
    {
        // TODO: Implement prepare_query() method.
    }

    /**
     * 构建第三方回调数据
     * @param $data
     * @return mixed
     */
    public function prepare_callback($data)
    {
        // TODO: Implement prepare_callback() method.
    }
}
