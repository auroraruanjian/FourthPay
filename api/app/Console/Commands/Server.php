<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Server extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'server:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '模拟服务端，推送第三方回调到平台！';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $server = new \Common\API\Server($this);
        $server->push_to_platform();
    }
}
