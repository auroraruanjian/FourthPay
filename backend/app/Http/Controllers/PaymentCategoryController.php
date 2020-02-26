<?php

namespace App\Http\Controllers;

use Common\Models\PaymentCategory;
use Common\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentCategoryController extends Controller
{
    private $filed = [
        'ident'         => '',
        'name'          => '',
        'method_idents' => [],
        'param'         => [],
        'status'        => true,
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
        $payment_category = PaymentCategory::orderBy('id','asc')->get();
        if( !$payment_category->isEmpty() ){
            $payment_category = $payment_category->toArray();
        }else{
            $payment_category = [];
        }

        $payment_methods = PaymentMethod::select(['ident','name'])->orderBy('id','asc')->get();
        $payment_methods = (!$payment_methods->isEmpty())?$payment_methods->toArray():[];

        $method_ids = array_column($payment_methods, 'ident');
        $payment_category = array_map(function($item)use($payment_methods,$method_ids){
            $item['method_idents'] = json_decode($item['method_idents'],true);
            $item['param'] = json_decode($item['param'],true);

            // 支付类型ID转成支付类型名称
            foreach($item['method_idents'] as &$_method){
                $_id = array_search($_method, $method_ids);
                $_method = $payment_methods[$_id]['name'];
            }

            return $item;
        },$payment_category);

        return $this->response(1,'Success',[
            'payment_category'  => $payment_category,
            'payment_methods'   => $payment_methods,
            'types'             => [
                [
                    'id'    => 0,
                    'name'  => '文本',
                ],
                [
                    'id'    => 1,
                    'name'  => '下拉',
                ]
            ]

        ]);
    }

    public function postCreate(Request $request)
    {
        $payment_category = new PaymentCategory();
        foreach( $this->filed as $filed => $default_val ){
            if(in_array($filed,['method_idents','param'])){
                $payment_category->$filed = json_encode($request->get($filed,$default_val));
            }else{
                $payment_category->$filed = $request->get($filed,$default_val);
            }
        }

        if( $payment_category->save() ){
            return $this->response(1,'添加成功');
        }else{
            return $this->response(0,'添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_category = PaymentCategory::find($id);

        if( empty($payment_category) ){
            return $this->response(0,'支付渠道不存在失败');
        }

        $payment_category = $payment_category->toArray();
        $payment_category['method_idents'] = json_decode($payment_category['method_idents'],true);
        $payment_category['param'] = json_decode($payment_category['param'],true);

        return $this->response(1,'success',$payment_category);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_category = PaymentCategory::find($id);

        if( empty($payment_category) ){
            return $this->response(0,'支付渠道不存在失败');
        }

        foreach( $this->filed as $filed => $default_val ){
            if(in_array($filed,['method_idents','param'])){
                $payment_category->$filed = json_encode($request->get($filed,$default_val));
            }else{
                $payment_category->$filed = $request->get($filed,$default_val);
            }
        }

        if( $payment_category->save() ) {
            return $this->response(1,'编辑成功');
        }else{
            return $this->response(0,'编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_category = PaymentCategory::find($id);

        if( empty($payment_category) ){
            return $this->response(0,'支付渠道不存在失败');
        }

        if( $payment_category->delete() ){
            return $this->response(1,'删除成功');
        }else{
            return $this->response(0,'删除失败');
        }
    }
}
