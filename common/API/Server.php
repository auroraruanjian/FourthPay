<?php

namespace Common\API;

use App\Model\PaymentOrder;
use Common\Models\Deposits;
use Common\Models\Merchants;
use Common\Models\PaymentChannelDetail;
use Illuminate\Console\Command;

class Server
{
    /**
     * @var Command
     */
    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    public function push_to_platform()
    {
        // 获取所有未处理的订单
        $deposit_ordre = Deposits::select([
            'id',
            'payment_channel_detail_id',
            'amount',
            //'merchant_order_no',
        ])
            ->where('status','0');

        $client = new \GuzzleHttp\Client([
            'connect_timeout'   => 5,
            'timeout'           => 30,
            'debug'             => false,
            'http_errors'       => false
        ]);

        $this->_info(date('Y-m-d H:i:s').'订单已开始处理。');

        $deposit_ordre->chunk(1000, function ($orders) use ($client) {
            foreach ($orders as $order) {
                $server_model = $this->getServerModel($order);

                $post_data = $server_model->callback($order->toArray());

                $callback_url = url('payment/callback', ['channel_detail_id'=>$order->payment_channel_detail_id]);

                if (isset($post_data['content-type']) && $post_data['content-type'] == 'application/json') {
                    $request_data['json']               = $post_data['data'];
                    $request_data['headers']            = ['content-type'=>'application/json'];
                } else {
                    if (is_array($post_data)) {
                        $request_data['form_params']    = $post_data;
                    } else {
                        $request_data['body']           = $post_data;
                    }
                }

                $response = $client->request(
                    'POST',
                    $callback_url,
                    $request_data
                );

                $this->_info(date('Y-m-d H:i:s').'订单已推送:');

                $this->_info('post_data');
                $this->_info(print_r($post_data, true));

                $this->_info('order:');
                $this->_info(print_r($order->toArray(), true));

                $this->_info('result:');
                $this->_info($response->getBody());
            }
        });

        $this->_info(date('Y-m-d H:i:s').'订单已全部处理完成。');
    }

    /**
     * 获取支付模型
     * @param $order
     * @return Object
     * @throws \Exception
     */
    private function getServerModel($order)
    {
        $payment = PaymentChannelDetail::select([
            "payment_category.ident",
            'payment_channel.channel_param',
        ])
            ->leftJoin('payment_channel','payment_channel.id','payment_channel_detail.payment_channel_id')
            ->leftJoin('payment_category','payment_category.id','payment_channel.payment_category_id')
            ->where('payment_channel_detail.id',$order->payment_channel_detail_id)
            ->first();

        if (!$payment) {
            abort_unless(config('app')['debug'], 404, '页面不存在！');
            throw new \Exception('通道不存在！');
        }

        $channel_param = json_decode($payment->channel_param);

        $ident = ucfirst($payment['ident']);

        $class = "Common\\API\\Server\\{$ident}\\{$ident}";

        try {
            $server_model_reflection= new \ReflectionClass($class);

            $server_model = $server_model_reflection->newInstance($channel_param);
        } catch (\Exception $e) {
            abort_unless(config('app')['debug'], 404, '页面不存在！');
            throw $e;
        }

        return $server_model;
    }

    //命令行输出信息
    private function _info($msg)
    {
        $this->command->info(date('Y-m-d H:i:s').$msg);
    }

    //命令行输出错误信息
    private function _error($msg)
    {
        $this->command->error(date('Y-m-d H:i:s').$msg);
    }
}
