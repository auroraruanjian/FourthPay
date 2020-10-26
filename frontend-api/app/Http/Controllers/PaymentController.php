<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use Common\Helpers\RSA;
use Common\Models\Merchants;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 支付页面显示
     */
    public function getIndex()
    {

    }

    /**
     *
     */
    public function postPay(Request $request)
    {
        // 支付金额
        $amount = $request->get('amount');

        // 支付方法
        $method = $request->get('method');

        // 支付银行
        $bank_code = $request->get('bank_code','');

        $merchant = Merchants::select(['id','account','merchant_private_key','md5_key'])->where('id',1)->first();
        if( empty($merchant) ){
            return $this->response(0,'对不起，商户不存在！');
        }

        // 获取支付商户，拼接支付URL，跳转
        $post_data = [
            'merchant_id'   => $merchant['account'],
            'amount'        => $amount,
            'notify_url'    => url('payment/notify'),
            'return_url'    => url('payment/return'),
            'order_no'      => Str::random(16),
            'goods_name'    => '充值',
            'method'        => $method,
            'ip'            => $request->ip(),
            'bank_code'     => $bank_code,
        ];

        $post_data['sign'] = md5_sign($post_data,$merchant['md5_key']);

        return $this->response(1,'success',[
            'url'   => 'http://api.laravel_admin.me/payment/pay?'.
                http_build_query([
                        'merchant_id'   => $merchant->account,
                        'data'          => RSA::private_encrypt( json_encode($post_data) , $merchant['merchant_private_key'] )
                    ]
            ),
        ]);
    }
}
