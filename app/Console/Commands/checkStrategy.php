<?php

namespace App\Console\Commands;

use App\Jobs\sendTS;
use App\Jobs\sendVS;
use App\Jobs\sendVT;
use Illuminate\Console\Command;

class checkStrategy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strategy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '检查stock是否满足策略，满足则发送短信+站内消息';

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
        //todo 时间策略队列
        sendTS::dispatch();
        //todo 防止短信连发同一手机被短信商当成系统出错拒绝
        sleep(10);
        //todo 值策略队列
        sendVS::dispatch();
    }
}
