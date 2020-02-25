<?php

namespace App\Http\Controllers;

use Common\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    private $filed = [
        'ident'     => '',
        'name'      => '',
        'status'    => true,
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
        $payment_channel = PaymentMethod::orderBy('id','asc')->get();
        if( !$payment_channel->isEmpty() ){
            $payment_channel = $payment_channel->toArray();
        }else{
            $payment_channel = [];
        }
        return $this->response(1,'Success',$payment_channel);
    }

    public function postCreate(Request $request)
    {
        $payment_channel = new PaymentMethod();
        foreach( $this->filed as $filed => $default_val ){
            $payment_channel->$filed = $request->get($filed,$default_val);
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

        $payment_channel = PaymentMethod::find($id);

        if( empty($payment_channel) ){
            return $this->response(0,'支付类型不存在失败');
        }

        $payment_channel = $payment_channel->toArray();

        return $this->response(1,'success',$payment_channel);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_channel = PaymentMethod::find($id);

        if( empty($payment_channel) ){
            return $this->response(0,'支付通道不存在失败');
        }

        foreach( $this->filed as $filed => $default_val ){
            $payment_channel->$filed = $request->get($filed,$default_val);
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

        $payment_channel = PaymentMethod::find($id);

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
