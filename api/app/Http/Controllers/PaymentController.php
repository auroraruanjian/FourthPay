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

            $this->payment_api = $payment_ref->newInstance();
        }catch(\ReflectionException $e){

            abort(403,'接口版本号错误！');
        }catch(\Exception $e){

            abort(403,'未知的异常！');
        }
    }

    // 支付
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
        // $data : 数据

        list($code,$message,$data) = $this->payment_api->pay($request);
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

    /**
     * 第三方异步回调
     * @param $channel_detail_id
     * @param Request $request
     * @return mixed
     */
    public function callback( $channel_detail_id, Request $request)
    {
        $res = $this->payment_api->callback($channel_detail_id, $request->all());
        return response()->json($res);
    }

    /**
     * 第三方页面跳转回调
     * @param $channel_detail_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string|\think\response\Redirect|void
     */
    public function callback_view( $channel_detail_id, Request $request)
    {
        $res = $this->payment_api->callback( $channel_detail_id, $request->all());
        // TODO：如果第三方接口不安全（同步跳转接口无签名、参数可修改、订单状态检查失败等情况）本地渲染支付成功页面(不修改订单数据库状态)，提示稍后到账
        // TODO：如果接口安全且订单状态检查成功则同步跳转到商户回调域名
        return redirect('');
    }

    /**
     * 商户查询订单状态记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function query(Request $request)
    {
        return response()->json($this->payment_api->query($request));
    }
}
