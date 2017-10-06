<?php

namespace App\Providers;

use App\Jobs\SaveCurDayGoldX;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::failing( function (SaveCurDayGoldX $event){
            $sms = new \App\Utils\Sms(env('ACCESS_KEY_ID'), env('ACCESS_KEY_SECRET'));
            $sms->sendSms("陈泽书1850255","SMS_101075057", 18502557106, ['typename'=>'失败', 'time'=> '321', 'num'=> 100]);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
