<?php

namespace Common\API;

use Common\Helpers\RSA;
use Common\Models\Deposits;
use Illuminate\Console\Command;

use App\Model\PaymentOrder;
use Illuminate\Support\Facades\DB;

class Crontab
{
    /**
     * @var Command
     */
    protected $command;

    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * 查询第三方订单状态
     */
    public function query()
    {
    }

    /**
     * 更新平台订单状态
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function push_to_platform()
    {
        $deposits = Deposits::select([
            'deposits.id',
            'deposits.merchant_order_no',
            'deposits.status',
            'deposits.amount',
            'deposits.real_amount',
            DB::raw('deposits.extra->>\'notify_url\' as notify_url'),
            'merchants.account',
            'merchants.system_private_key',
            'merchants.md5_key',
            'payment_method.ident as method'
        ])
        ->leftJoin('payment_channel_detail','payment_channel_detail.id','deposits.payment_channel_detail_id')
        ->leftJoin('payment_method','payment_method.id','payment_channel_detail.payment_method_id')
        ->leftJoin('merchants','merchants.id','deposits.merchant_id')
        ->where([
            ['deposits.status','=',2],
            ['deposits.push_status','=',0]
        ]);


        $client = new \GuzzleHttp\Client([
            'connect_timeout'   => 5,
            'timeout'           => 30,
            'debug'             => false,
            'http_errors'       => false
        ]);

        $deposits->chunk(1000, function ($deposit_orders) use ($client) {
            foreach ($deposit_orders as $order) {
                try {
                    $post_data = [
                        'order_no'          => $order->merchant_order_no,
                        'method'            => $order->method,
                        'amount'            => $order->amount,
                        'real_amount'       => $order->real_amount,
                        'platform_order_id' => id_encode($order->id),
                        'status'            => $order->status,
                    ];

                    // 增加签名
                    $post_data['sign'] = md5_sign($post_data,$order->md5_key);

                    $response = $client->request(
                        'POST',
                        $order->notify_url,
                        [
                            'form_params' => [
                                'merchant_id'   => $order->account,
                                'data'          => RSA::private_encrypt(json_encode($post_data),$order->system_private_key)
                            ]
                        ]
                    );

                    if ($response->getStatusCode() === 200) {
                        $status = 1;
                        if( $response->getBody()->getContents() === 'success' ){
                            $status = 88;
                        }

                        $result = Deposits::where('id', '=', $order->id)
                                ->update([
                                    'push_status'   => $status,
                                    'push_at'       => date('Y-m-d H:i:s')
                                ]);

                        if (empty($result) || $result < 1) {
                            $this->_error("   订单号：{$order->id} 状态：{$status} 更新失败！");
                        } else {
                            $this->_info("   订单号：{$order->id} 状态：{$status} 更新成功！");
                        }
                    } else {
                        $msg  = "   订单号：{$order->id} 推送失败".PHP_EOL;

                        foreach ($response->getHeaders() as $name => $values) {
                            $msg .= $name . ': ' . implode(', ', $values) . PHP_EOL;
                        }

                        $msg .= "body:".$response->getBody().PHP_EOL;

                        $this->_error($msg);
                    }
                } catch (\Exception $e) {
                    if (config('app.debug')) {
                        throw $e;
                    }

                    $this->_error("   订单号：{$order->id} 推送失败！".$e->getMessage());
                }
            }
        });
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
