<?php

namespace App\Jobs;

use App\Models\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class RefreshConfig implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //开始刷新网站配置
        Redis::del('sys_config');
        Redis::pipeline(function ($pipe) {
            $config_list = Config::select(['key','value'])->where([['parent_id','!=',0],['is_disabled','=',1]])->get();
            foreach($config_list as $config){
                $pipe->hset("sys_config", $config->key,$config->value);
            }
        });
    }
}
