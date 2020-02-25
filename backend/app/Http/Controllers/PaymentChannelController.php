<?php

namespace App\Http\Controllers;

use Common\Models\PaymentCategory;
use Common\Models\PaymentChannel;
use Common\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentChannelController extends Controller
{
    private $filed = [
        'name'                  => '',
        'payment_category_id'   => '',
        'top_user_ids'          => [],
        'channel_param'         => [],
        'methods_param'         => [],
        'status'                => true,
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getIndex()
    {
        $payment_channel = PaymentChannel::select([
            'payment_channel.id',
            'payment_channel.name',
            'payment_channel.payment_category_id',
            'payment_channel.top_user_ids',
            'payment_channel.channel_param',
            'payment_channel.methods_param',
            'payment_channel.status',
            'payment_category.name as payment_category_name'
        ])
            ->leftJoin('payment_category','payment_category.id','payment_channel.id')
            ->orderBy('id','asc')
            ->get();

        if( !$payment_channel->isEmpty() ){
            $payment_channel = $payment_channel->toArray();
        }else{
            $payment_channel = [];
        }

        $payment_category = PaymentCategory::select(['id','name','methods','param','status'])->orderBy('id','asc')->get();
        $payment_category = (!$payment_category->isEmpty())?$payment_category->toArray():[];

        $payment_methods = PaymentMethod::select(['id','ident','name'])->orderBy('id','asc')->get();
        $payment_methods = (!$payment_methods->isEmpty())?$payment_methods->toArray():[];

        $method_ids = array_column($payment_methods, 'id');
        $payment_category = array_map(function($item)use($payment_methods,$method_ids){
            $item['methods'] = json_decode($item['methods'],true);
            $item['param'] = json_decode($item['param'],true);

            // 支付类型ID转成支付类型名称
            foreach($item['methods'] as $_key => $_method){
                $_id = array_search($_method, $method_ids);
                $item['methods'][$_key] = [
                    'ident' => $payment_methods[$_id]['ident'],
                    'name'  => $payment_methods[$_id]['name'],
                ];
            }

            return $item;
        },$payment_category);

        return $this->response(1,'Success',[
            'payment_channel'   => $payment_channel,
            'payment_category'  => $payment_category
        ]);
    }

    public function postCreate(Request $request)
    {
        $payment_channel = new PaymentChannel();
        foreach( $this->filed as $filed => $default_val ){
            if( in_array($filed,['top_user_ids','channel_param','methods_param']) ){
                $payment_channel->$filed = json_encode($request->get($filed,$default_val));
            }else{
                $payment_channel->$filed = $request->get($filed,$default_val);
            }
        }

        if( $payment_channel->save() ){

            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_channel = PaymentChannel::find($id);

        if( empty($payment_channel) ){
            return $this->response(0,'支付通道不存在失败');
        }

        $payment_channel = $payment_channel->toArray();
        $payment_channel['top_user_ids'] = json_decode($payment_channel['top_user_ids'],true);
        $payment_channel['channel_param'] = json_decode($payment_channel['channel_param'],true);
        $payment_channel['methods_param'] = json_decode($payment_channel['methods_param'],true);

        return $this->response(1,'success',$payment_channel);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_channel = PaymentChannel::find($id);

        if( empty($payment_channel) ){
            return $this->response(0,'支付通道不存在失败');
        }

        foreach( $this->filed as $filed => $default_val ){
            if( in_array($filed,['top_user_ids','channel_param','methods_param']) ){
                $payment_channel->$filed = json_encode($request->get($filed,$default_val));
            }else{
                $payment_channel->$filed = $request->get($filed,$default_val);
            }
        }

        if( $payment_channel->save() ) {
            return $this->response(1,'编辑成功');
        }else{
            return $this->response(0,'编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_channel = PaymentChannel::find($id);

        if( empty($payment_channel) ){
            return $this->response(0,'支付通道不存在失败');
        }

        if( $payment_channel->delete() ){
            return $this->response(1,'删除成功');
        }else{
            return $this->response(0,'删除失败');
        }
    }
}
