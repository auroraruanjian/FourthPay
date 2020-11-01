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

    /**
     * 获取订单信息
     * @param $data
     */
    public function getThirdOrder( $data )
    {
        return [
            // 平台订货单号
            'order_no'          => $data['id'],
            // 第三方订单号
            'third_order_no'    => $data['order_no'],
            // 真实支付金额
            'real_amount'       => $data['amount'],
        ];
    }

    public function getResponse($pay_status)
    {
        // TODO: Implement getResponse() method.

        return 'success';
    }
}
