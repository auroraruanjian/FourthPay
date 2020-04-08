<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Common\API\MerchantFund;
use Common\Models\Deposits;
use Common\Models\Orders;
use Illuminate\Http\Request;
use DB;

class DepositsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getIndex(Request $request)
    {
        $page  = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit');

        $start = ($page - 1) * $limit;

        $data = [
            'total'       => 0,
            'deposits'    => [],
        ];

        $deposits = Deposits::select([
            'deposits.id',
            'merchants.account',
            //'deposits.payment_channel_detail_id',
            'payment_channel.name as payment_channel_name',
            'payment_method.name as payment_method_name',
            'deposits.amount',
            'deposits.real_amount',
            'deposits.manual_amount',
            'deposits.merchant_order_no',
            //'deposits.third_order_no',
            'deposits.order_id',
            'deposits.accountant_admin',
            'deposits.cash_admin',
            'deposits.status',
            'deposits.push_status',
            //'deposits.push_at',
            'deposits.done_at',
            'deposits.created_at'
        ])
            ->leftJoin('payment_channel_detail','payment_channel_detail.id','deposits.payment_channel_detail_id')
            ->leftJoin('payment_channel','payment_channel.id','payment_channel_detail.payment_channel_id')
            ->leftJoin('payment_method','payment_method.id','payment_channel_detail.payment_method_id')
            ->leftJoin('merchants','merchants.id','deposits.merchant_id')
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Deposits::count();

        if (!$deposits->isEmpty()) {
            $data['deposits'] = $deposits->toArray();
            foreach( $data['deposits'] as $key => &$val ){
                $val['id'] = id_encode($val['id']);
            }
        }

        return $this->response(1, 'Success!', $data);
    }

    /**
     * 人工审核
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetail(Request $request)
    {
        $id = $request->get('id');

        $deposit = Deposits::select([
            'deposits.account_number',
            'merchants.account as merchant_account',
            'payment_channel.name as payment_channel_name',
            'payment_method.name as payment_method_name',
            'deposits.created_at',
            'deposits.ip',
            'deposits.merchant_fee',
            'deposits.third_fee',
            'deposits.amount',
            'deposits.real_amount',
            'deposits.remark',
            'deposits.manual_amount',
            'deposits.merchant_fee',
            'deposits.manual_postscript',
            'deposits.third_order_no',
            'deposits.accountant_admin',
            'deposits.deal_at',
        ])
            ->leftJoin('payment_channel_detail','payment_channel_detail.id','deposits.payment_channel_detail_id')
            ->leftJoin('payment_channel','payment_channel.id','payment_channel_detail.payment_channel_id')
            ->leftJoin('payment_method','payment_method.id','payment_channel_detail.payment_method_id')
            ->leftJoin('merchants','merchants.id','deposits.merchant_id')
            ->where('deposits.id','=',id_decode($id))
            ->first();

        if (empty($deposit)) {
            return $this->response(0, '记录不存在');
        }

        $deposit = $deposit->toArray();
        $deposit['id'] = $id;

        return $this->response(1, 'success', $deposit);
    }

    public function putDeal(Request $request)
    {
        $id = $request->get('id');

        // 获取订单，检查订单状态
        $deposit = Deposits::where('id',id_decode($id))->first();
        if( $deposit->status == 0 ){
            $deposit->manual_amount = $request->get('manual_amount');
            $deposit->merchant_fee = $request->get('merchant_fee');
            $deposit->manual_postscript = $request->get('manual_postscript');
            $deposit->third_order_no = $request->get('third_order_no');
            $deposit->status = 1;
            $deposit->accountant_admin = auth()->user()->username;
            $deposit->deal_at = (string)Carbon::now();

            if($deposit->save()){
                return $this->response(1, '操作成功');
            }
        }

        return $this->response(0, '操作失败');
    }

    public function putVerify(Request $request)
    {
        $id = $request->get('id');
        $status = $request->get('status');  // 限制 2 3

        // 获取订单，检查订单状态
        $deposit = Deposits::where('id',id_decode($id))->first();
        if( $deposit->status == 1 ){
            if( $status === '2' ){
                DB::beginTransaction();

                // TODO:增加账变
                $order = new Orders();
                $order->from_merchant_id = $deposit->merchant_id;
                $order->admin_user_id = auth()->id();
                $order->amount = $deposit->manual_amount;
                $order->comment = $deposit->admin_remark;
                $order->ip = request()->ip();
                if (!MerchantFund::modifyFund($order, 'ZXCZ')) {
                    DB::rollback();
                    return $this->response(1, MerchantFund::$error_msg);
                }

                $deposit->status = $status;
                $deposit->cash_admin = auth()->user()->username;
                $deposit->done_at = (string)Carbon::now();
                $deposit->order_id = $order->id;

                if($deposit->save()){
                    DB::commit();
                    return $this->response(1, '操作成功');
                }
                DB::rollback();
            }elseif( $status === '3' ){
                $deposit->status = $status;
                $deposit->cash_admin = auth()->user()->username;

                if($deposit->save()){
                    return $this->response(1, '操作成功');
                }
            }
        }

        return $this->response(0, '操作失败');
    }

    public function getPush(Request $request)
    {
        // TODO：推送

    }
}
