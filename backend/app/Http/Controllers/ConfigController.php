<?php

namespace App\Http\Controllers;

use App\Events\NotifyEvent;
use App\Http\Requests\CommonIndexRequest;
use App\Http\Requests\ConfigCreateRequest;
use App\Models\Config;
use Illuminate\Http\Request;
use App\Jobs\RefreshConfig;
use Cache;
use Illuminate\Support\Carbon;

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
    public function postIndex(CommonIndexRequest $request) {
        $page  = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit');

        $start = ($page - 1) * $limit;

        $parent_id = (int)$request->get('parent_id', 0);

        $data = [
            'total'       => 0,
            'config_list' => [],
        ];

        $configlist = Config::select([
            'id',
            'parent_id',
            'title',
            'key',
            'value',
            'type',
            'extra',
            'is_disabled',
            'description'
        ])
            ->where('parent_id', '=', $parent_id)
            ->orderBy('id', 'asc')
            ->skip($start)
            ->take($limit)
            ->get();

        $data['total'] = Config::count();

        if (!$configlist->isEmpty()) {
            foreach($configlist as &$config){
                $config->extra = json_decode($config->extra,true);
                if( $config->type == 3 ){
                    $config->value = ($config->value==1)?true:false;
                }else if( $config->type == 1 && isset($config->extra['encrypt']) && $config->extra['encrypt']){
                    $config->value = '******';
                }
            }
            $data['config_list'] = $configlist->toArray();
        }

        if( $parent_id == 0 ){
            $data['top_config'] = $data['config_list'];
        }else{
            $data['top_config'] = Config::select(['id','title'])->where('parent_id',0)->get()->toArray();
        }
        array_unshift($data['top_config'],['id'=>0,'title'=>'主配置项']);

        // 加载上次配置刷新信息
        $data['last_refresh'] = Cache::store('redis')->get('last_refresh_config');

        return $this->response(1, 'Success!', $data);
    }

    public function postCreate(ConfigCreateRequest $request)
    {
        $config              = new Config();
        $config->parent_id   = $request->get('parent_id')??0;
        $config->title       = $request->get('title','');
        $config->key         = $request->get('key','');
        $config->value       = $request->get('value','');
        $config->type        = $request->get('type','1');
        $config->extra       = $request->get('extra');
        $config->description = $request->get('description','');
        $config->is_disabled = (int)$request->get('is_disabled',0)?true:false;

        if( $config->save() ){
            $this->refresh();
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

        // $config->extra = json_decode($config->extra,true);
        if( $config->type == 3 ){
            $config->value = ($config->value==1)?true:false;
        }else if( $config->type == 1 && isset($config->extra['encrypt']) && $config->extra['encrypt']){
            $config->value = '******';
        }
        $config->type   = (string)$config->type;
        $config         = $config->toArray();

        return $this->response(1, 'success', $config);
    }

    public function putSetting(Request $request)
    {
        $id = (int)$request->get('id');

        $config = Config::find($id);

        if (empty($config)) {
            return $this->response(0, '配置不存在失败');
        }

        $config->parent_id   = $request->get('parent_id',0);
        $config->title       = $request->get('title','');
        $config->key         = $request->get('key','');
        $config->value       = $request->get('value','');
        $config->type        = $request->get('type','1');
        $config->extra       = $request->get('extra');
        $config->description = $request->get('description','');
        $config->is_disabled = (int)$request->get('is_disabled',0)?true:false;

        if ($config->save()) {
            $this->refresh();
            return $this->response(1, '设置成功');
        } else {
            return $this->response(0, '设置失败');
        }
    }

    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id');

        $config = Config::find($id);

        if (empty($config)) {
            return $this->response(0, '配置不存在失败');
        }

        $config->value       = $request->get('value','');
        $config->is_disabled = (int)$request->get('is_disabled',0)?true:false;

        if ($config->save()) {
            $this->refresh();
            return $this->response(1, '编辑成功');
        } else {
            return $this->response(0, '编辑失败');
        }
    }

    private function refresh()
    {
        RefreshConfig::dispatch(auth()->user()->username)->onConnection('redis');
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
