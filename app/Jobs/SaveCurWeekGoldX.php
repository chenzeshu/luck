<?php

namespace App\Jobs;

use App\Http\Controllers\v1\X\WeekController;
use App\Models\stock;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class SaveCurWeekGoldX implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout= 900;
    public $tries = 3;

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
    public function handle(WeekController $weekController)
    {
        DB::table('weekxes')->truncate();
        $time1= time();

        $stocks = stock::where('status', 0)->get()->toArray();

        //目前只有python工具可以爬到上市时间， 暂时使用固定起始时间；
        $now = date('Ymd', time());
        $data = [];
        foreach ($stocks as $k => $v){
            $_data = [];
            $_data = $weekController->getX($v['code'], "20050505", $now);
            if(count($_data) != 0) {
                foreach ($_data as $m => $n) {
                    $data[] = [
                        'date' => $n['date'],
                        'macd' => $n['macd'],
                        'stock_id' => $v['id']
                    ];
                }
                if (count($data) > 200) {
                    DB::table('weekxes')->insert($data);
                    $data = null;
                    $data = [];
                }
            }
            $_data = null;
            sleep(0.1);

        }

        DB::table('weekxes')->insert($data);

        $this->job->delete();
    }
}
