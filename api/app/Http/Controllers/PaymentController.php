<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private $payment_api;

    public function __construct(Request $request)
    {
        // 接口版本号
        $version      = $request->get('version');
        if( empty($version) || !preg_match("/^V[0-9]{0,3}(\.[0-9]{0,3}){1,2}$/",$version) ){
            abort(403,'接口版本号错误！');
        }

        $class_name = 'Common\\API\\'.(($version!='V1.0')?$version.'\\':'').'Payment';

        // 根据版本号构建接口模型
        try{
            $payment_ref = new \ReflectionClass($class_name);

            $this->payment_api = $payment_ref->newInstance($request);
        }catch(\ReflectionException $e){

            abort(403,'接口版本号错误！');
        }catch(\Exception $e){

            abort(403,$e->getMessage().',code:'.$e->getCode());
        }
    }

    //支付
    public function pay(Request $request)
    {
        /*
        // 构建请求测试代码
        $request_data = [
            // 金额
            'amount'            => 100.01,
            // 异步回调地址
            'callback_url'      => 'http://www.baidu.com',
            // 同步回调地址
            'callback_url_view' => 'http://www.baidu.com',
            // 商户订单号
            'order_no'          => Str::random(10),
            // 商品名
            'goods_name'        => 'payment',
            // 支付方式,对应payment_method表
            'method'            => 'wechat_scan',
        ];

        // 签名
        $request_data['sign'] = $this->payment_api->_sign($request_data);

        dd($this->payment_api->_encrypt($request_data));
        */

        // $code > 0 成功，其他统一失败
        // $message：错误消息
        //
        list($code,$message,$data) = $this->payment_api->pay();
        dd($data);

        // 解析参数
//        switch( $data['type'] ){
//            case $this->payment_api::PAY_TYPE_HTML:
//                $a= '';
//                break;
//            case $this->payment_api::PAY_TYPE_STRING:
//                $a= '';
//                break;
//            case $this->payment_api::PAY_TYPE_URL:
//                $a= '';
//                break;
//            case $this->payment_api::PAY_TYPE_QRCODE:
//                $a= '';
//                break;
//            default:;
//        }
//
//        return response()->json(['222']);
    }

    public function query(Request $request)
    {

    }
}
