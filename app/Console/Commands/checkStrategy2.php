<?php

namespace App\Console\Commands;

use App\Jobs\sendVS;
use Illuminate\Console\Command;

class checkStrategy2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'strategy:value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '不和时间命令放在一起， 因为发送短信间隔太短会被遮蔽';

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
        //todo 值策略队列
        sendVS::dispatch();
    }
}
