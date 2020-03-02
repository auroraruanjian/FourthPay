<?php

namespace App\Console\Commands;

use Common\API\Crontab;
use Illuminate\Console\Command;

class Payment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '向商户推送已经支付成功的订单';

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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle()
    {
        //
        $crontab = new Crontab($this);
        $crontab->push_to_platform();
    }
}
