<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\CommonIndexRequest;
use Common\Models\MerchantFund;
use Common\Models\Merchants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchantController extends Controller
{
    //
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function postIndex(CommonIndexRequest $request) {
        $page  = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit');

        $start = ($page - 1) * $limit;

        $data = [
            'total'       => 0,
            'client_list' => [],
        ];

        $clientlist = Merchants::select([
            'merchants.id',
            'merchants.account',
            'merchants.status',
            'merchant_fund.balance',
            'merchant_fund.hold_balance',
        ])
            ->leftJoin('merchant_fund','merchant_fund.merchant_id','merchants.id')
            ->orderBy('merchants.id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Merchants::count();

        if (!$clientlist->isEmpty()) {
            $data['client_list'] = $clientlist->toArray();
        }

        return $this->response(1, 'Success!', $data);
    }

    public function postCreate(ClientCreateRequest $request)
    {
        DB::beginTransaction();
        $merchant             = new Merchants();
        $merchant->account    = $request->get('account')??0;
        $merchant->status     = (int)$request->get('status',0)?true:false;

        if( $merchant->save() ){
            // 新增商户资金记录
            $merchant_fund = new MerchantFund();
            $merchant_fund->merchant_id = $merchant->id;

            // TODO:新增商户系统超级管理员

            if($merchant_fund->save()){
                DB::commit();
                return $this->response(1, '添加成功');
            }
        }

        DB::rollBack();
        return $this->response(0, '添加失败');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $client = Merchants::find($id);

        if (empty($client)) {
            return $this->response(0, '配置不存在');
        }

        $client = $client->toArray();

        return $this->response(1, 'success', $client);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $client = Merchants::find($id);

        if (empty($client)) {
            return $this->response(0, '配置不存在失败');
        }

        $client->account     = $request->get('account',0);
        $client->status      = (int)$request->get('status',0)?true:false;

        if ($client->save()) {
            return $this->response(1, '编辑成功');
        } else {
            return $this->response(0, '编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = (int)$request->get('id');
        if( Merchants::where('id','=',$id)->delete() ){
            return $this->response(1,'删除成功！');
        }else{
            return $this->response(0,'删除失败！');
        }
    }
}
