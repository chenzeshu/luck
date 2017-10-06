<?php

namespace App\Console\Commands;

use App\Jobs\SaveCurDayGoldX;
use Illuminate\Console\Command;

class updateDayStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:day';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '为了避免2核机器在supervisor多开线程下挂掉，把day更新这个内存CPU都耗的工作跟其他2个错开';

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
        SaveCurDayGoldX::dispatch();
    }
}
