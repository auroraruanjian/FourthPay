<?php

namespace App\Http\Controllers;

use Common\Helpers\RSA;
use Common\Models\Merchants;
use Common\Models\Orders;
use Common\Models\PaymentChannel;
use Common\Models\PaymentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    private $payment_api;

    public function __construct(Request $request)
    {
        // 接口版本号
        $version      = $request->get('version');
        if( !empty($version) && !preg_match("/^V[0-9]{0,3}(\.[0-9]{0,3}){1,2}$/",$version) ){
            abort(403,'接口版本号错误！');
        }

        $class_name = 'Common\\API\\'.((!empty($version)&&$version!='V1.0')?$version.'\\':'').'Payment';

        // 根据版本号构建接口模型
        try{
            $payment_ref = new \ReflectionClass($class_name);

            $this->payment_api = $payment_ref->newInstance();
        }catch(\ReflectionException $e){

            abort(403,'接口版本号错误2！');
        }catch(\Exception $e){

            abort(403,'未知的异常！');
        }
    }

    // 支付
    public function pay(Request $request)
    {
        /**
         * $code    : 类型
         * $message : 消息内容
         * $data    : 数据
         */
        list($code , $message ,$data) = $this->payment_api->pay($request);

        if( $code != 1 ){
            return $this->response($code,$message);
        }

         // 解析参数
        switch( $data['type'] ){
            case PAY_VIEW_TYPE_HTML:
                return view('pay', [
                    'request_url'   => $this->payment_api->pay_model->request_url,
                    'data'          => $data['data'],
                ]);
            case PAY_VIEW_TYPE_RAW:
                return $data['data'];
            case PAY_VIEW_TYPE_URL:
                return redirect()->away($data['data']);
            case PAY_VIEW_TYPE_QRCODE:
                return view('qrcode_pay', [
                    'order_no'      => ssl_encrypt($data['order_no'], env('SECURITY_KEY')),
                    'pay_cn_name'   => $this->payment_api->pay_model->getCnname($data['bank_code']),
                    'bank_code'     => $data['bank_code'],
                    'amount'        => $data['amount'],
                    'qrcode'        => $data['url'],
                    'type'          => $data['type'],
                ]);
            default:
                return $this->response(-1,'未知的异常！');
        }
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
        return $this->payment_api->getResponse( $res['pay_status'] );
    }

    /**
     * 第三方页面跳转回调
     * @param $channel_detail_id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string|\think\response\Redirect|void
     */
    public function callback_view( $channel_detail_id, Request $request)
    {
        $res = $this->payment_api->callback($channel_detail_id, $request->all());
        // TODO：如果第三方接口不安全（同步跳转接口无签名、参数可修改、订单状态检查失败等情况）本地渲染支付成功页面(不修改订单数据库状态)，提示稍后到账
        // TODO：如果接口安全且订单状态检查成功则同步跳转到商户回调域名

        // TODO: same as payment callback cli request data

        return redirect($res['return_url']);
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

    public function test(Request $request)
    {
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

        $merchant = Merchants::select(['merchant_private_key','md5_key'])->first()->toArray();

        // 签名
        $request_data['sign'] = md5_sign($request_data,$merchant['md5_key']);

        dd(RSA::private_encrypt( json_encode($request_data) , $merchant['merchant_private_key'] ));
    }
}
