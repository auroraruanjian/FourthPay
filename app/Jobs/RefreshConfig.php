<?php

namespace App\Jobs;

use App\Models\Config;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;
use Cache;

class RefreshConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $username = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $username )
    {
        //
        $this->username = $username;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 开始刷新网站配置
        Redis::del('sys_config');
        Redis::pipeline(function ($pipe) {
            $config_list = Config::select(['key','value'])->where([['parent_id','!=',0],['is_disabled','=',1]])->get();
            foreach($config_list as $config){
                $pipe->hset("sys_config", $config->key,$config->value);
            }
        });

        // 记录刷新时间
        Cache::store('redis')->forever('last_refresh_config', ['username'=>$this->username,'time'=>date('Y-m-d H:i:s')]);
    }
}
