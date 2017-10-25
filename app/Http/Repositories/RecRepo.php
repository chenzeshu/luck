<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/7
 * Time: 18:23
 */

namespace App\Http\Repositories;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class RecRepo
{
    /**
     * 把$data数组里的日期转换为diffForHumans格式
     * @param $data
     * @return mixed
     */
    public function useCarbonTrans($data)
    {
        foreach ($data as $k => $stock){
            $data[$k]['date'] = Carbon::createFromFormat('Y-m-d H:i:s', $stock['date'])->diffForHumans();
        }
        return $data;
    }

    /**
     * 对复合数据的日期全部转换为diffForHumans格式
     * @param $data
     */
    public function useCarbonTrans2Dimention($data)
    {
        $data = $this->useCarbonTrans($data);

        foreach ($data as $k => $stock){
            $data[$k]['weekxes'] = $this->useCarbonTrans($stock['weekxes']);
        }

        return $data;
    }
    /**
     * 存入缓存
     * 1. 方便分页
     * 2. 前段提醒当前搜索条件是几个月前
     * @param $cacheName 存入缓存是几个月前的值的键名
     */
    public function cacheAffair($request, $cacheName){
        //当前台传来的是翻页请求（get）时，没有$request，此时使用缓存预先存储的月份时间来做筛选。
        $wanTime = $request->wanTime == "" ? getC('wantime') : $request->wanTime;
        $wanTime2 = $request->wanTime2 == "" ? getC('wantime2') : $request->wanTime2;
        $wanTime3 = $request->wanTime3 == "" ? getC('wantime3') : $request->wanTime3;

        //2缓存为空则使用当下时间
        if(getC("wantime2") == ""){
            $wanTime2 = date('Y-m-d H:i:s', time());
        }

        if(getC("wantime3") == ""){
            $wanTime3 = $wanTime;
        }

        $_now = date('Y-m-d', time());
        $diff = getMonthNum($wanTime, $_now);
        $diff2 = getMonthNum($wanTime2, $_now);
        Cache::put($cacheName, $diff, 3600);
        Cache::put($cacheName.'2', $diff2, 3600);
        Cache::put('wantime', $wanTime, 3600);
        Cache::put('wantime2', $wanTime2, 3600);
        Cache::put('wantime3', $wanTime3, 3600);

        return [$wanTime, $wanTime2, $wanTime3];
    }
}