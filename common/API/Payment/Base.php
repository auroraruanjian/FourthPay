<?php
namespace Common\API\Payment;

abstract class Base
{
    public function __construct( $channel )
    {

    }

    /**
     * 构建支付数据
     * @param $data
     * @return mixed
     */
    abstract public function prepare_pay( $data );

    /**
     * 构建订单查询数据
     * @param $data
     * @return mixed
     */
    abstract public function prepare_query( $data );

    /**
     * 构建第三方回调数据
     * @param $data
     * @return mixed
     */
    abstract public function prepare_callback( $data );
}
