<?php

namespace App\Console\Commands;

use App\Jobs\SaveCurMonGoldX;
use App\Jobs\SaveCurWeekGoldX;
use Illuminate\Console\Command;

class updateStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新股票的叉数据';

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

        SaveCurWeekGoldX::dispatch();
        SaveCurMonGoldX::dispatch();
        echo "更新中...";
    }
}
