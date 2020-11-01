<?php
namespace Common\API;

use Carbon\Carbon;
use Common\Helpers\RSA;
use Common\Models\Deposits;
use Common\Models\Merchants;
use Common\Models\PaymentChannelDetail;
use Common\Models\PaymentMethod;
use Illuminate\Http\Request;
use Validator;

class Payment
{
    // 商户信息
    private $merchant       = [];

    // 解密的数据
    private $decrypt_data   = [];

    // 错误消息
    public $error_message   = '';

    // 支付模型
    public $pay_model;

    public function __construct()
    {
    }

    /**
     * 支付
     * @return array
     * @throws Exception
     */
    public function pay(Request $request)
    {
        list($init_code,$init_message) = $this->_init( 'pay' ,$request);
        if( $init_code != 1 ){
            return [ $init_code, $init_message , []];
        }

        // 检查订单号是否存在
        if( Deposits::where([
                ['merchant_id','=',$this->merchant['id']],
                ['merchant_order_no',$this->decrypt_data['order_no']],
            ])
            ->count() > 0
        ){
            return [-8,'订单号已存在' , []];
        }

        // 获取支付通道
        $channel_detail = $this->getPaymentChannel();
        if( !$channel_detail ){
            // TODO:触发系统告警-运营,将错误消息发送给平台运营人员

            return [-9,$this->error_message , []];
        }

        $account_number = $channel_detail['channel_param']['merchant_id']??$channel_detail['channel_param']['appid'];

        // 添加支付订单记录
        $deposits_model = new Deposits();
        $deposits_model->merchant_id = $this->merchant['id'];                                       // 商户ID
        $deposits_model->payment_channel_detail_id = $channel_detail['channel_detail_id'];          // 支付通道ID
        $deposits_model->account_number = $account_number;                                          // 支付商户号
        //merchant_fee  从商户号分配费率计算
        //third_fee     第三方手续费从$channel_detail中计算
        $deposits_model->amount = $this->decrypt_data['amount'];                                    // 金额
        $deposits_model->merchant_order_no = $this->decrypt_data['order_no'];                       // 订单号
        $deposits_model->ip = request()->ip();                                                      // IP
        $deposits_model->created_at = (string)Carbon::now();                                        // 申请时间
        $deposits_model->extra = json_encode([
            'notify_url'        => $this->decrypt_data['notify_url'],
            'return_url'        => $this->decrypt_data['return_url'],
            'goods_name'        => $this->decrypt_data['goods_name'],
            'method'            => $this->decrypt_data['method'],
            'bank_code'         => $this->decrypt_data['bank_code']??'',
        ]);

        try {
            // 保存订单记录
            $deposits_model->save();
        } catch (\PDOException $e) {
            \Log::error($e);
            // TODO:触发系统告警-程序
            // 保存数据出错发送警报信息给系统管理员

            return [-10,'数据写入失败！' , []];
        }

        // TODO: 支付提交到第三方
        // 获取支付模型，构建支付数据
        $this->pay_model = $this->getPaymentModel($channel_detail);
        if( !$this->pay_model ){
            return [-11,'模型获取失败！' , []];
        }

        // TODO: 构造支付参数
        list($pay_type,$message,$pay_data) =  $this->pay_model->prepare_pay([
            // 支付金额
            'amount'    => $this->decrypt_data['amount'],
            // 平台订单号
            'order_no'  => id_encode($deposits_model->id),
            // 商品名
            'good_name' => $this->decrypt_data['goods_name'],
            // 支付类型
            'method'    => $this->decrypt_data['method'],
            // 支付银行
            'bank_code' => $this->decrypt_data['bank_code']??'',
            // 客户IP
            'ip'        => $this->decrypt_data['ip'],
        ]);

        if( $pay_type == PAY_VIEW_TYPE_ERROR ){
            return [ -12, $message, []];
        }

        return [ 1, $message, [
            'type'  => $pay_type,
            'data'  => $pay_data
        ]];
    }

    /**
     * 商户查询订单状态记录
     * @param Reqeust $reqeust
     * @return array
     */
    public function query(Reqeust $request)
    {
        $return_data = [
            'code'      => '',
            'message'   => '',
            'data'      => '',
        ];

        list($init_code,$init_message) = $this->_init( 'query' , $request );
        if( $init_code != 1 ){
            $return_data['code'] = $init_code;
            $return_data['message'] = $init_message;
            return $return_data;
        }

        // 获取 平台订单号，商户订单号，金额，实际支付金额，支付时间，支付状态
        $deposit_record = Deposits::select([
            'deposits.id',
            'payment_method.ident as method',
            'deposits.amount',
            'deposits.real_amount',
            'deposits.status',
            'deposits.done_at',
            'deposits.created_at'
        ])
            ->leftJoin('payment_method','payment_method.id','deposits.payment_method_id')
            ->where('deposits.merchant_order_no',$this->decrypt_data['order_no'])
            ->first();

        if( empty($deposit_record) ){
            $return_data['code'] = -8;
            $return_data['message'] = '订单号不存在';
            return $return_data;
        }

        $deposit_record = $deposit_record->toArray();

        // 此处订单ID加密转码返回
        $deposit_record['id'] = id_encode($deposit_record['id']);
        // 增加签名
        $deposit_record['sign'] = md5_sign($deposit_record,$this->merchant['md5_key']);

        // 返回加密签名后的数据
        $return_data['code'] = 0;
        $return_data['message'] = 'success';
        $return_data['data'] = $this->_encrypt($deposit_record);
        return $return_data;
    }

    /**
     * 第三方支付回调
     * @param int $channel_detail_id
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function callback(int $channel_detail_id , $request_data )
    {
        // 获取订单号,检查订单状态
        $return_data = [
            'money'      => 0,
            'order_no'   => '',
            'pay_status' => false,
            'return_url' => '',
        ];

        // 获取支付通道
        $details = PaymentChannelDetail::select([
            'payment_channel.id as channel_id',
            'payment_channel.channel_param',
            'payment_channel_detail.id as channel_detail_id',
            'payment_category.ident as category_ident'
        ])
            ->leftJoin('payment_channel','payment_channel.id','payment_channel_detail.payment_channel_id')
            ->leftJoin('payment_category','payment_category.id','payment_channel.payment_category_id')
            ->where('payment_channel_detail.id','=',$channel_detail_id)
            ->first();

        if( empty($details) ){
            \Log::error('通道不存在', ['third_order'=>$channel_detail_id,'data'=>$request_data]);
            return $return_data;
        }

        // TODO: 支付提交到第三方
        // 获取支付模型，构建支付数据
        $this->pay_model = $this->getPaymentModel($details);
        if( !$this->pay_model ){
            \Log::error('模型获取失败', ['third_order'=>$channel_detail_id,'data'=>$request_data]);
            return $return_data;
        }

        // 记录日志


        // 如果是服务器回调，检查是否IP白名单
        if( request()->route()->getActionMethod() != 'callback_view' ){
            if( !$this->pay_model->is_allow_ip(request()->ip())) {
                \Log::error('IP ERROR', ['third_order'=>$channel_detail_id,'data'=>$request_data]);
                return $return_data;
            }
        }

        $third_order = $this->pay_model->getThirdOrder($request_data);
        if (empty($third_order['order_no'])) {
            \Log::error('订单号为空', ['third_order'=>$third_order]);
            return $return_data;
        }

        // 获取数据库订单记录
        if( request()->route()->getActionMethod() == 'callback_view' ){
            $deposit_record = Deposits::select([
                'deposits.id',
                'deposits.amount',
                'deposits.real_amount',
                'deposits.merchant_order_no',
                'deposits.status',
                'deposits.callback_status',
                DB::raw("deposits.extra->>'return_url'"),
                'merchants.account',
                'merchants.system_private_key',
                'merchants.md5_key',
                'payment_method.ident as method'
            ])
                ->leftJoin('payment_channel_detail','payment_channel_detail.id','deposits.payment_channel_detail_id')
                ->leftJoin('payment_method','payment_method.id','payment_channel_detail.payment_method_id')
                ->leftJoin('merchants','merchants.id','deposits.merchant_id')
                ->where([
                    ['deposits.id','=',id_decode($third_order['order_no'])],
                ])
                ->first();
        }else{
            $deposit_record = Deposits::select([
                'deposits.id',
                'deposits.amount',
                'deposits.real_amount',
                'deposits.merchant_order_no',
                'deposits.status',
                'deposits.callback_status',
                DB::raw("deposits.extra->>'return_url'"),
            ])
                ->where([
                    ['deposits.id','=',id_decode($third_order['order_no'])],
                ])
                ->first();
        }
        //订单不存在
        if (empty($deposit_record)) {
            \Log::error('订单不存在', ['third_order'=>$third_order]);
            return $return_data;
        }

        // 金额检查是否相同

        $return_data['money']       = $deposit_record->amount;
        $return_data['order_no']    = $deposit_record->merchant_order_no;
        $return_data['return_url']  = $deposit_record->return_url;

        if( request()->route()->getActionMethod() == 'callback_view' ){
            // create callback view data
            $post_data = [
                'order_no'          => $deposit_record->merchant_order_no,
                'method'            => $deposit_record->method,
                'amount'            => $deposit_record->amount,
                'real_amount'       => $deposit_record->real_amount,
                'platform_order_id' => id_encode($deposit_record->id),
                'status'            => $deposit_record->status,
            ];

            // 增加签名
            $post_data['sign'] = md5_sign($post_data,$deposit_record->md5_key);

            $symbol = '?';
            if( strpos($return_data['return_url'],'?') ){
                $symbol = '&';
            }

            $return_data['return_url'] = $return_data['return_url'].$symbol.http_build_query([
                'merchant_id'   => $deposit_record->account,
                'data'          => RSA::private_encrypt(json_encode($post_data),$deposit_record->system_private_key)
            ]);
        }

        // 检查是否已支付成功
        if ( $deposit_record->status == 2 ) {
            $return_data['pay_status']  = true;
            return $return_data;
        }

        // 检查回调结果
        if( $this->pay_model->check_callback($request_data) ){
            $query_status = $this->pay_model->query( $deposit_record );
            if( in_array($query_status,[
                $this->pay_model::QUERY_CHECK_SUCCESS,
                $this->pay_model::QUERY_CHECK_UNDEFINED,
            ],true)  ){
                // 通过- 修改订单状态，增加账变记录
                $deposit_record->status = 2;
                $deposit_record->callback_status = 1;
                $deposit_record->callback_at = (string)Carbon::now();
                //
                if( !empty($third_order['third_order_no']) ) {
                    $deposit_record->third_order_no = $third_order['third_order_no'];
                }
                if( !empty($third_order['real_amount']) ){
                    $deposit_record->real_amount = $third_order['real_amount'];
                }

                $deposit_record->done_at = (string)Carbon::now();
                if( $deposit_record->save() ){
                    // TODO 增加账变

                    $return_data['pay_status']  = true;
                    return $return_data;
                }
            }
        }

        // 失败
        return $return_data;
    }

    /**
     * 获取支付通道
     * @return bool
     */
    private function getPaymentChannel()
    {
        // 获取支付类型，检查支付类型是否状态
        $payment_method = PaymentMethod::select(['id','status'])->where('ident',$this->decrypt_data['method'])->first();
        if( empty($payment_method) ){
            $this->error_message = '支付类型不存在！';
            return false;
        }
        if( !$payment_method->status ){
            $this->error_message = '支付类型已关闭！';
            return false;
        }

        $now_time = Carbon::now()->format('H:i:s');

        $details = PaymentChannelDetail::select([
            'payment_channel.id as channel_id',
            'payment_channel.max_amount',
            'payment_channel.channel_param',
            'payment_channel_detail.extra',
            'payment_channel_detail.id as channel_detail_id',
            'payment_channel_detail.payment_method_id',
            'payment_category.ident as category_ident',
        ])
            ->leftJoin('payment_channel','payment_channel.id','payment_channel_detail.payment_channel_id')
            ->leftJoin('payment_category','payment_category.id','payment_channel.payment_category_id')
            ->where([
                ['payment_channel_detail.min_amount','<=',$this->decrypt_data['amount']],
                ['payment_channel_detail.max_amount','>=',$this->decrypt_data['amount']],
                ['payment_channel_detail.payment_method_id','=',$payment_method->id],
                ['payment_channel_detail.status','=',true],
                ['payment_channel_detail.top_merchant_ids','@>',$this->merchant['id']],
                ['payment_channel.status','=',0],
            ])
//            ->where( function($query)use($now_time){
//                // 正常时间区间
//                $query->where([
//                    ['payment_channel_detail.start_time','<=',$now_time],
//                    ['payment_channel_detail.end_time','>=',$now_time],
//                ])
//                // 如果跨天，当前时间大于开始时间
//                ->orWhereRaw(" ( payment_channel_detail.start_time > payment_channel_detail.end_time AND payment_channel_detail.start_time <= ? ) ",[$now_time])
//                // 如果跨天，当前时间小于结束时间
//                ->orWhereRaw(" ( payment_channel_detail.start_time > payment_channel_detail.end_time AND payment_channel_detail.end_time >= ? ) ",[$now_time])
//                // 或者开始时间和结束时间相同
//                ->orWhereRaw("payment_channel_detail.start_time = payment_channel_detail.end_time");
//            })
            ->get();

        if( $details->isEmpty() ){
            $this->error_message = '没有符合条件的支付通道！';
            return false;
        }

        if( $details->count() > 1){
            // TODO: 匹配策略
            // TODO：获取通道当天累计充值金额，判断是否超额
            /*
             * 匹配支付通道,匹配规则
             * 1.随机获取支付通道
             * 2.匹配当天充值金额最低的通道
             * 3.优先选择低费率通道
             */

            $detail_data = $details->first()->toArray();
        }else{
            $detail_data = $details->first()->toArray();
        }

        $detail_data['channel_param'] = json_decode($detail_data['channel_param'],true);

        return $detail_data;
    }

    /**
     * 获取商户信息
     * @param string $merchant_id 商户号
     * @return array|boolean
     */
    private function getMerchant( $merchant_id )
    {
        // 根据用户商户号获取商户信息
        $merchant = Merchants::select([
            'id',
            'account',
            //'system_public_key',
            //'system_private_key',
            'merchant_public_key',
            //'merchant_private_key',
            'md5_key'
        ])
            ->where('account',$merchant_id)
            ->first();

        // 商户号不存在
        if( empty($merchant) ) {
            return false;
        }

        return  $merchant->toArray();
    }

    /**
     * 商户请求初始化
     * @param string $api_name
     * @param Request $request
     * @param array $keys
     * @return array
     */
    private function _init(string $api_name, Request $request)
    {
        $validator = $this->_validator('request',$request->all());

        // 验证失败
        if( !$validator['status'] ) {
            return [-1, $validator['message']];
        };

        $this->merchant = $this->getMerchant( $request->get('merchant_id') );
        if( !$this->merchant ){
            return [-3, '商户不存在！'];
        }

        // 商户使用商户私钥加密数据请求平台接口，平台使用商户公钥解密数据
        $this->decrypt_data = $this->_decrypt($request->get('data'));

        // 如果数据解密失败
        if( !$this->decrypt_data ){
            return [-4, '数据解密失败！'];
        }
        if( $this->merchant['account'] != $this->decrypt_data['merchant_id'] ){
            return [-5, '对不起，账户校验失败！'];
        }

        $validator = $this->_validator($api_name,$this->decrypt_data);
        // 验证失败
        if( !$validator['status'] ) {
            return [-6,$validator['message']];
        };

        $keys = [];
        if($api_name == 'pay'){
            $keys = ['merchant_id','amount','notify_url','return_url','order_no','goods_name','method','ip','bank_code'];
        }elseif($api_name == 'query'){
            $keys = ['merchant_id','order_no'];
        }
        $keys[] = 'sign';

        $request_data = [];
        foreach($keys as $key){
            $request_data[$key] = $this->decrypt_data[$key];
        }

        // 如果是在线网银，则需要对应的银行代码
        if( $api_name=='pay' && $request_data['method'] == 'netbank' ){
            $request_data['bank_code'] = $this->decrypt_data['bank_code'];
        }

        // 验证MD5签名
        if( !md5_verify($request_data,$this->merchant['md5_key']) ){
            // 签名验证失败
            return [-7,'签名校验失败！'];
        }

        return [1, 'success'];
    }

    /**
     * 获取支付模型
     * @param $ident
     * @param $channel
     * @return object
     * @throws /Exception
     */
    private function getPaymentModel($channel)
    {

        $ident = ucfirst($channel['category_ident']);

        $class = "Common\\API\\Payment\\{$ident}\\{$ident}";

        try {
            $this->pay_model_reflection = new \ReflectionClass($class);

            return $this->pay_model_reflection->newInstance($channel);
        } catch (Exception $e) {
            \Log::error($e);
            return false;
        }
    }

    public function getResponse( $pay_status )
    {
        return $this->pay_model->getResponse( $pay_status );
    }

    /**
     * 加密参数
     * @param array $data 需要加密的参数
     * @return string
     */
    public function _encrypt( $data )
    {
        return RSA::private_encrypt( json_encode($data) , $this->merchant['system_private_key'] );
    }

    /**
     * 解密参数
     * @param string $string 加密的参数
     * @return array
     */
    public function _decrypt( $string )
    {
        $decrypt_string = RSA::public_decrypt( $string , $this->merchant['merchant_public_key'] );
        if( $decrypt_string ){
            return json_decode($decrypt_string,true);
        }
        return false;
    }

    /**
     * 校验数据
     * @param string $api_name
     * @param array $data
     * @return array [status,message] status:通过为true，失败为false，message：消息
     */
    private function _validator( $api_name , $data )
    {
        $rule       = [];
        $messages   = [];

        // 原始加密请求验证
        if( $api_name == 'request' ){
            // 验证参数
            $rule = [
                'merchant_id'   => 'bail|required|alpha_dash|between:8,16',
                'data'          => 'bail|required|string',
            ];

            $messages = [
                'merchant_id.required'      => '商户号不能为空！',
                'merchant_id.alpha_dash'    => '商户号格式不正确！',
                'merchant_id.between'       => '商户号格式不正确！',
                'data.required'             => '数据不能为空！',
                'data.string'               => '数据格式不正确！',
            ];
            // 解密后的支付请求数据
        }elseif( $api_name == 'pay' ){
            // 验证数据类型是否正确
            $rule = [
                'amount'            => 'bail|required|numeric|min:0.01',
                'notify_url'        => 'bail|required|url|max:255',
                'return_url'        => 'bail|required|url|max:255',
                'order_no'          => 'bail|required|alpha_dash|between:8,32',
                'method'            => 'bail|required|alpha_dash|exists:payment_method,ident',
                'bank_code'         => 'bail|required_if:method,netbank|alpha',
                'ip'                => 'bail|required|ip',
                'sign'              => 'bail|required|string',
            ];

            $messages = [
                'amount.required'               => '金额不能为空！',
                'amount.numeric'                => '金额类型不正确！',
                'amount.min'                    => '金额不正确！',
                'notify_url.required'           => '异步回调地址不能不空！',
                'notify_url.url'                => '异步回调地址格式不正确！',
                'notify_url.max'                => '异步回调地址长度不能超过255个字符！',
                'return_url.required'           => '同步回调地址不能不空！',
                'return_url.url'                => '同步回调地址格式不正确！',
                'return_url.max'                => '同步回调地址长度不能超过255个字符！',
                'order_no.required'             => '订单号不能为空！',
                'order_no.alpha_dash'           => '订单号格式不正确！',
                'order_no.between'              => '订单号长度不正确！',
                'method.required'               => '支付类型不能为空！',
                'method.alpha_dash'             => '支付类型格式不正确！',
                'method.exists'                 => '支付类型不存在！',
                'bank_code.required_if'         => '银行代码不能为空！',
                'bank_code.alpha'               => '银行代码格式不正确！',
                'ip.required'                   => 'IP不能为空！',
                'ip.ip'                         => 'IP格式不正确！',
                'sign.required'                 => '签名不能为空！',
                'sign.string'                   => '签名格式不正确！',
            ];
        }elseif( $api_name == 'query' ){

        }

        $validator = Validator::make($data, $rule , $messages);

        $result = [
            'status'    => true,
            'message'   => 'success',
        ];

        if ($validator->fails()) {
            foreach($validator->errors()->all() as $error){
                $result['status']   = false;
                $result['message']  = $error;
                break;
            }
        }

        return $result;
    }
}
