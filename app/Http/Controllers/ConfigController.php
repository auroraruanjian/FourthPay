<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    //
    public function postIndex(Request $request) {
        $page  = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit');

        $start = ($page - 1) * $limit;

        $parent_id = (int)$request->get('parent_id', 0);

        $data = [
            'total'       => 0,
            'config_list' => [],
        ];

        $adminlist = Config::select([
            'id',
            'parent_id',
            'title',
            'key',
            'value',
            'is_disabled'
        ])
            ->where('parent_id', '=', $parent_id)
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Config::count();

        if (!$adminlist->isEmpty()) {
            $data['adminlist'] = $adminlist->toArray();
        }

        return $this->response(1, 'Success!', $data);
    }

    public function postCreate(Request $request)
    {
        $config              = new Config();
        $config->parent_id   = $request->get('parent_id',0);
        $config->title       = $request->get('title','');
        $config->key         = $request->get('key','');
        $config->value       = $request->get('value','');
        $config->description = $request->get('description','');
        $config->is_disabled = (int)$request->get('is_disabled',0)?true:false;

        if( $config->save() ){
            return $this->response(1, '添加成功');
        } else {
            return $this->response(0, '添加失败');
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $config = Config::find($id);

        if (empty($config)) {
            return $this->response(0, '配置不存在');
        }

        $config         = $config->toArray();

        return $this->response(1, 'success', $config);
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $config = Config::find($id);

        if (empty($config)) {
            return $this->response(0, '管理员不存在失败');
        }

        $config->parent_id   = $request->get('parent_id',0);
        $config->title       = $request->get('title','');
        $config->key         = $request->get('key','');
        $config->value       = $request->get('value','');
        $config->description = $request->get('description','');
        $config->is_disabled = (int)$request->get('is_disabled',0)?true:false;

        if ($config->save()) {
            return $this->response(1, '编辑成功');
        } else {
            return $this->response(0, '编辑失败');
        }
    }

    public function deleteDelete(Request $request)
    {
        $id = $request->get('id');
        if( Config::where('id','=',$id)->delete() ){
            return $this->response(1,'删除成功！');
        }else{
            return $this->response(0,'删除失败！');
        }
    }
}
