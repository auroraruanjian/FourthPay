<?php
namespace Common\API\Server;

abstract class Base
{
    // 商户参数
    protected $channel_param;

    /**
     * Server constructor.
     * @param $channel
     * @throws \Exception
     */
    public function __construct($channel_param)
    {
        if (empty($channel_param)) {
            throw new \Exception('商户不存在', '404');
        }

        $this->channel_param = $channel_param;
    }

    /**
     * 模拟第三方支付接口
     * @param $data
     */
    public function pay($data)
    {
        return $data;
    }

    /**
     * 订单查询接口,客户主动查询订单支付状态
     * @param  array 请求信息
     * @return array 响应参数
     */
    abstract public function query($data);

    /**
     * 主动推送订单支付完成状态到客户callback_server_url
     * @param $order array 订单记录
     * @return array 返回请求参数
     */
    abstract public function callback($order);
}
