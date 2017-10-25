<?php

namespace App\Jobs;

use App\Http\Controllers\v1\MACDController;
use App\Models\stock;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class SaveX implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; //100条50秒， 3000条约1500秒 给300秒余地
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
    public function handle(MACDController $m)
    {
        DB::table('dayxes')->truncate();
        DB::table('weekxes')->truncate();
        DB::table('monthxes')->truncate();

        $stocks = stock::where('status', 0)->offset(0)->limit(850)->get()->toArray();
        $now = date('Ymd', time());
        $day = $week = $month = [];
        foreach ($stocks as $k=>$v){
            $_dayx  = $_weekx = $_monthx = [];
            list($_dayx, $_weekx, $_monthx) = $m->dragDataFromWY($v['code'], '20050505', $now);

            if(!empty($_dayx)){
                foreach ($_dayx as $dx){
                    $day[] = [
                        'date' => $dx['date'],
                        'macd' => $dx['macd'],
                        'diff' => $dx['diff'],
                        'stock_id' => $v['id']
                    ];
                }
            }

            if(!empty($_weekx)){
                $week[] = [
                    'date' => $_weekx[0]['date'],
                    'macd' => $_weekx[0]['macd'],
                    'diff' => $_weekx[0]['diff'],
                    'stock_id' => $v['id']
                ];
            }

            if(!empty($_monthx)){
                $month[] = [
                    'date' => $_monthx[0]['date'],
                    'macd' => $_monthx[0]['macd'],
                    'diff' => $_monthx[0]['diff'],
                    'stock_id' => $v['id']
                ];
            }

            if (count($day) > 100) {
                DB::table('dayxes')->insert($day);
                DB::table('weekxes')->insert($week);
                DB::table('monthxes')->insert($month);
                unset($day, $week, $month);
                $day = $week = $month = [];
            }
            unset($_dayx, $_weekx, $_monthx);
//            system('sync && echo 3 > /proc/sys/vm/drop_caches');

        }
        DB::table('dayxes')->insert($day);
        DB::table('weekxes')->insert($week);
        DB::table('monthxes')->insert($month);

        $this->job->delete();
    }
}
