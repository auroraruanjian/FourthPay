<?php
namespace Common\API\Payment;

abstract class Base
{
    // 商户参数
    protected $channel_param;

    // 支付请求URL
    public $request_url;

    // 回调请求URL
    public $query_url;

    // 平台异步回调URL
    protected $platform_notify_url;

    // 同步请求返回URL
    protected $platform_return_url;

    const QUERY_CHECK_SUCCESS            = 1;      // 第三方订单查询成功
    const QUERY_CHECK_ERROR              = -1;     // 第三方订单查询失败
    const QUERY_CHECK_UNDEFINED          = 0;      // 第三方未定义订单查询接口

    //第三方通知IP白名单列表
    public $white_ip_list               = [];

    public $order_id_filed          = '';   //第三方商户订单号字段
    public $third_order_id_filed    = '';   //第三方平台订单号字段
    public $third_amount_filed      = '';   //第三方回调金额字段
    public $third_amount_type       = 1;    //第三方金額模式 1:元 0.1角 0.01分

    //平台和第三方银行对照数组
    public $bank_codes  = [
        /*收银台*/
        'CASHIER'      => '',

        /*网银支付*/
        //农业银行
        'ABC'           => '',
        //工商银行
        'ICBC'          => '',
        //建设银行
        'CCB'           => '',
        //交通银行
        'BOCOM'         => '',
        //中国银行
        'BOC'           => '',
        //招商银行
        'CMB'           => '',
        //民生银行
        'CMBC'          => '',
        //光大银行
        'CEB'           => '',
        //兴业银行
        'CIB'           => '',
        //邮政银行
        'PSBC'          => '',
        //平安银行
        'PAB'           => '',
        //浦发银行 上海浦东发展银行
        'SPDB'          => '',
        //中信银行
        'CNCB'          => '',
        //广发银行
        'GDB'           => '',
        //华夏银行
        'HXB'           => '',
        //北京银行
        'BOB'           => '',
        //渤海银行
        'CBHB'          => '',
        //东亚银行
        'HKBEA'         => '',
        //宁波银行
        'NCBC'          => '',
        //北京农村商业银行
        'BNCB'          => '',
        //南京银行
        'NJCB'          => '',
        //浙商银行
        'CZBANK'        => '',
        //上海银行
        'SHBANK'        => '',
        //上海农村商业银行
        'SNCB'          => '',
        //杭州银行
        'HCCB'          => '',
        //浙江江稠州商业银行
        'ZJJZB'         => '',
        //深圳发展银行
        'SDB'           => '',

        /*扫码支付*/
        //微信扫码
        'WEIXIN_SCAN'   => '',
        //支付宝扫码
        'ALIPAY_SCAN'   => '',
        //QQ扫码
        'QQ_SCAN'       => '',
        //京东扫码
        'JD_SCAN'       => '',
        //银联扫码
        'UNIONPAY_SCAN' => '',

        /*H5支付*/
        //微信H5
        'WEIXIN_H5'     => '',
        //支付宝H5
        'ALIPAY_H5'     => '',
        //QQ H5
        'QQ_H5'         => '',
        //京东H5
        'JD_H5'         => '',
        //银联H5
        'UNIONPAY_H5'   => '',

        /*条码支付*/
        //微信条码
        'WEIXIN_BARCODE' => '',

        /*信用卡和快捷支付*/
        //快捷支付
        'QUICK'         => '',
        //信用卡
        'CREDIT'        => '',

        /*手机类型*/
        //手机微信扫码
        'M_WEIXIN_SCAN' => '',
        //手机支付宝扫码
        'M_ALIPAY_SCAN' => '',
        //手机网银
        'M_CASHIER'     => '',

        /* 区块链类型 */
        //数字货币
        'DIGITAL_CURRENCY' => '',
    ];

    public function __construct( $channel )
    {

    }

    /**
     * 构建支付数据
     * @param $data
     * @return Array
     */
    abstract public function prepare_pay( $data );

    /**
     * 构建订单查询数据
     * @param $data
     * @return mixed
     */
    abstract public function query( $data );

    /**
     * 检查第三方回调数据
     * @param $data
     * @return mixed
     */
    abstract public function check_callback( $data );

    /**
     * 获取返回数据
     * @param $pay_status
     * @return mixed
     */
    abstract public function getResponse( $pay_status );

    /**
     * 检查是否百名单IP
     * @param $ip
     * @return bool
     */
    public function is_allow_ip( $ip )
    {
        if (in_array($ip, $this->white_ip_list, true) || empty($this->white_ip_list)) {
            return true;
        }
        return false;
    }

    /**
     * 获取订单信息
     * @param $data
     */
    public function getThirdOrder( $data )
    {
        $order_ids = [
            'order_id'          => '',
            'third_order_id'    => '',
            'amount'            => null,
            'third_amount'      => null,
        ];
        if (!empty($data[$this->third_order_id_filed])&&$this->third_order_id_filed!='') {
            $order_ids['third_order_id'] = $data[$this->third_order_id_filed];
        }

        if (isset($data[$this->third_amount_filed])&&$this->third_amount_filed!='') {
            $order_ids['amount'] = $data[$this->third_amount_filed] * $this->third_amount_type;
            //第三方实际充值金额
            $order_ids['third_amount'] = $data[$this->third_amount_filed] * $this->third_amount_type;
        }

        if (!empty($data[$this->order_id_filed])&&$this->order_id_filed!='') {
            $order_ids['order_id'] = $data[$this->order_id_filed];
            return $order_ids;
        }

        return false;
    }
}
