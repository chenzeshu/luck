<?php

namespace App\Jobs;

use App\Models\refer\myTime;
use App\msg;
use App\Utils\Sms;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class sendTS implements ShouldQueue
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
        $now = date('Y-m-d H:i:s', strtotime("-1 day"));
        $TS = myTime::with('favorite.stock')->get()->toArray();
        $sms = new Sms(env('ACCESS_KEY_ID'),env('ACCESS_KEY_SECRET'));
        $typename = null;
        $num = null;
        foreach ($TS as $stock){
            if($stock['refertime'] < $now && $stock['known'] == 0 ){
                $typename .= "|". $stock['favorite']['stock']['name']."|";
                $num = "|". $stock['msg']."|";
            }
        }

        if(!empty($typename)&& !empty($num)){
            //todo 发送短信
            $templateParam = [
                'typename' =>$typename,
                'num' => $num,
                'time' => 1,
            ];

            $phoneNumber = "18502557106";
//            $phoneNumber = "18057091878";
            $sms->myConvenience($phoneNumber, $templateParam);
            //由于不知道queue_name， 下列三步暂时放弃
                //todo 确认短信送达
                //todo 每秒确认一次
                //todo 若10秒内未送达，更换其他API

            //todo 发送站内信
            msg::create([
                'code'=> $stock['favorite']['stock']['code'],
                'type'=> '到时间',
                'desc'=> $stock['msg'],
            ]);
        }

        $this->job->delete();
    }
}
