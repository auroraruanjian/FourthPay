<?php

namespace App\Http\Controllers;

use Common\Models\PaymentCategory;
use Common\Models\PaymentChannel;
use Common\Models\PaymentChannelDetail;
use Common\Models\PaymentMethod;
use Illuminate\Http\Request;
use DB;

class PaymentChannelController extends Controller
{
    private $filed = [
        'name'                  => '',
        'payment_category_id'   => '',
        'channel_param'         => [],
        'status'                => true,
    ];

    private $detail_filed = [
        'top_merchant_ids'  => [],
        'start_time'        => '00:00:00',
        'end_time'          => '00:00:00',
        'rate'              => 0,
        'max_amount'        => 0,
        'min_amount'        => 0,
        'status'            => true,
        'payment_method_id' => '',
        'extra'             => [],
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
            'payment_channel.channel_param',
            'payment_channel.status',
            'payment_category.name as payment_category_name'
        ])
            ->leftJoin('payment_category','payment_category.id','payment_channel.payment_category_id')
            ->orderBy('id','asc')
            ->get()
            ->map(function( $channel ){
                $detail = PaymentChannelDetail::select([
                    'payment_channel_detail.payment_method_id',
                    'payment_channel_detail.rate',
                    'payment_channel_detail.min_amount',
                    'payment_channel_detail.max_amount',
                    'payment_channel_detail.status',
                    'payment_method.ident',
                    'payment_method.name'
                ])
                    ->leftJoin('payment_method', 'payment_method.id', 'payment_channel_detail.payment_method_id')
                    ->where('payment_channel_detail.payment_channel_id', $channel->id)
                    ->get();
                $channel->detail = $detail->isEmpty() ? [] : $detail->toArray();

                $channel_param = json_decode($channel->channel_param, true);
                unset($channel_param['key1']);
                unset($channel_param['key1']);

                $channel->channel_param = $channel_param;
                $channel->status = ($channel->status==0)?true:false;

                return $channel;
            });
        $payment_channel = !$payment_channel->isEmpty()?$payment_channel->toArray():[];

        $payment_category = PaymentCategory::select([
            'id',
            'name',
            'method_idents',
            'param',
            'status'
        ])
            ->orderBy('id','asc')
            ->get()
            ->map( function( $category ){
                $method_idents = json_decode($category->method_idents,true);
                $category_method_idents = PaymentMethod::select([
                    'id as payment_method_id',
                    'name as payment_method_name',
                    'ident as payment_method_ident',
                    'status as payment_method_status'
                ])
                    ->whereIn('ident',$method_idents)
                    ->get();
                $category->method_idents = !$category_method_idents->isEmpty()?$category_method_idents->toArray():[];
                $category->param = json_decode($category->param,true);
                return $category;
            });
        $payment_category = !$payment_category->isEmpty()?$payment_category->toArray():[];

        return $this->response(1,'Success',[
            'payment_channel'   => $payment_channel,
            'payment_category'  => $payment_category
        ]);
    }

    public function postCreate(Request $request)
    {
        $payment_channel = new PaymentChannel();
        foreach( $this->filed as $filed => $default_val ){
            if( in_array($filed,['channel_param']) ){
                $payment_channel->$filed = json_encode($request->get($filed,$default_val));
            }elseif( $filed == 'status' ){
                $payment_channel->$filed = $request->get($filed,$default_val)?0:1;
            }else{
                $payment_channel->$filed = $request->get($filed,$default_val);
            }
        }

        DB::beginTransaction();

        if( !$payment_channel->save() ){
            DB::rollBack();
            return $this->response(0,'编辑失败');
        }

        // 保存detail
        foreach($request->get('detail') as $detail){
            $model_detail = new PaymentChannelDetail();
            $model_detail->payment_channel_id = $payment_channel->id;

            foreach( $this->detail_filed as $filed => $default_val ){
                if( in_array($filed,['top_merchant_ids','extra']) ){
                    // TODO:检查类型是银行卡时保存banks

                    $model_detail->$filed = json_encode($detail[$filed]??$default_val);
                }else{
                    $model_detail->$filed = $detail[$filed]??$default_val;
                }
            }
            if(!$model_detail->save()){
                DB::rollBack();
                return $this->response(0,'保存失败');
            }
        }

        DB::commit();
        return $this->response(1,'添加成功');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $payment_channel = PaymentChannel::find($id);

        if( empty($payment_channel) ){
            return $this->response(0,'支付通道不存在失败');
        }

        // 加载商户接口详情数据
        foreach($payment_channel->detail as &$dateil){
            $dateil->top_merchant_ids = json_decode($dateil->top_merchant_ids,true);
            $dateil->extra = json_decode($dateil->extra,true);
        };

        $payment_channel = $payment_channel->toArray();
        $payment_channel['channel_param'] = json_decode($payment_channel['channel_param'],true);
        $payment_channel['status'] = $payment_channel['status']==0?true:false;

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
            if( in_array($filed,['channel_param']) ){
                $payment_channel->$filed = json_encode($request->get($filed,$default_val));
            }elseif( $filed == 'status' ){
                $status = $request->get($filed,$default_val);
                // 如果数据库开启，用户提交关闭
                // 如果数据库关闭，用户提交开启
                // 更新数据库结果
                if( ($payment_channel->$filed == 0 && !$status) || ($payment_channel->$filed > 0 && $status) ){
                    $payment_channel->$filed = $status?0:1;
                }
            }else{
                $payment_channel->$filed = $request->get($filed,$default_val);
            }
        }

        DB::beginTransaction();

        if( !$payment_channel->save() ){
            DB::rollBack();
            return $this->response(0,'编辑失败');
        }

        $request_detail = $request->get('detail');

        // 保存detail
        foreach($request_detail as $detail){
            if(!empty($detail['id'])){
                $model_detail = PaymentChannelDetail::find($detail['id']);
            }else{
                $model_detail = new PaymentChannelDetail();
                $model_detail->payment_channel_id = $payment_channel->id;
            }

            foreach( $this->detail_filed as $filed => $default_val ){
                if( in_array($filed,['top_merchant_ids','extra']) ){
                    // TODO:检查类型是银行卡时保存banks

                    $model_detail->$filed = json_encode($detail[$filed]??$default_val);
                }else{
                    $model_detail->$filed = $detail[$filed]??$default_val;
                }
            }
            if(!$model_detail->save()){
                DB::rollBack();
                return $this->response(0,'ID:'.$detail['id'].' 保存失败');
            }
        }

        // 删除不需要保存的通道详情
        PaymentChannelDetail::whereNotIn('id',array_column($request_detail,'id'))->where('payment_channel_id',$payment_channel->id)->delete();

        DB::commit();
        return $this->response(1,'编辑成功');
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
