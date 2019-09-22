<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientCreateRequest;
use App\Http\Requests\CommonIndexRequest;
use App\Models\Clients;
use Illuminate\Http\Request;

class ClientController extends Controller
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

        $clientlist = Clients::select([
            'id',
            'account',
            'status'
        ])
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Clients::count();

        if (!$clientlist->isEmpty()) {
            $data['client_list'] = $clientlist->toArray();
        }

        return $this->response(1, 'Success!', $data);
    }

    public function postCreate(ClientCreateRequest $request)
    {
        $client             = new Clients();
        $client->account    = $request->get('account')??0;
        $client->status     = (int)$request->get('status',0)?true:false;

        if( $client->save() ){
            return $this->response(1, '添加成功');
        } else {
            return $this->response(0, '添加失败');
        }
    }

    public function getEdit(ClientCreateRequest $request)
    {
        $id = (int)$request->get('id');

        $client = Clients::find($id);

        if (empty($client)) {
            return $this->response(0, '配置不存在');
        }

        $client = $client->toArray();

        return $this->response(1, 'success', $client);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $client = Clients::find($id);

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
        if( Clients::where('id','=',$id)->delete() ){
            return $this->response(1,'删除成功！');
        }else{
            return $this->response(0,'删除失败！');
        }
    }
}
