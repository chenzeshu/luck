<?php

namespace App\Jobs;

use App\Http\Controllers\v1\X\DayController;
use App\Models\stock;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class SaveCurDayGoldX implements ShouldQueue
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

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(DayController $dayController)
    {
        DB::table('dayxes')->truncate();
//        $time1 = time();

        $stocks = stock::where('status', 0)->get()->toArray();
        $now = date('Ymd', time());
        $data = [];
//        $count = 0;  //数据数目
        foreach ($stocks as $k => $v){
            $_data = $dayController->getX($v['code'], "20050505", $now);
//            $count += count($_data);
            if(count($_data) != 0) {
                foreach ($_data as $m => $n) {
                    $data[] = [
                        'date' => $n['date'],
                        'macd' => $n['macd'],
                        'stock_id' => $v['id']
                    ];
                }
                if (count($data) > 1000) {
                    DB::table('dayxes')->insert($data);
                    $data = null;
                    $data = [];
                }
            }
            $_data = null;
            $_data = [];
            sleep(0.1);
        }
        DB::table('dayxes')->insert($data);


//        $time2 = time();
//        $time = $time2 - $time1;  //耗时
//        session(['time'=>$time]);

        $this->job->delete();
        //短信通知(国庆因阿里云放假， 改用session)
//        $sms = new \App\Utils\Sms(env('ACCESS_KEY_ID'), env('ACCESS_KEY_SECRET'));
//        $re = $sms->sendSms("陈泽书1850255","SMS_101075057", 18502557106, ['typename'=>'3000日数据', 'time'=> $time, 'num'=> $count]);
    }
}
