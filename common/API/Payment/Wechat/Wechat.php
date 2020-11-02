<?php
namespace Common\API\Payment\Wechat;

use Common\API\Payment\Base;

class Wechat extends Base
{
    public $order_id_filed          = 'id';         //商户订单号字段
    public $third_order_id_filed    = 'order_no';   //第三方平台订单号字段
    public $third_amount_filed      = 'amount';     //第三方回调金额字段

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
    public function query($data)
    {
        // TODO: Implement prepare_query() method.

        // request to server check order status

        return parent::QUERY_CHECK_SUCCESS;
    }

    /**
     * 构建第三方回调数据
     * @param $data
     * @return mixed
     */
    public function check_callback($data)
    {
        // TODO: Implement prepare_callback() method.
        // check callback status

        // check sign

        return true;
    }

    public function getResponse($pay_status)
    {
        // TODO: Implement getResponse() method.

        return $pay_status?'success':'error';
    }
}
