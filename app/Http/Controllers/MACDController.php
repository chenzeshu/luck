<?php

namespace App\Http\Controllers;

use App\Http\Controllers\v1\X\DayController;
use App\Http\Controllers\v1\X\MonthController;
use App\Jobs\testSMS;
use App\Models\monthx;
use App\Models\refer\myTime;
use App\Models\refer\myValue;
use App\Models\stock;
use App\msg;
use App\Utils\Sms;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use QL\QueryList;

require_once(base_path()."/vendor/jaeger/querylist/src/QueryList.php");

class MACDController extends Controller
{
    protected $DATE = [];
    protected $DIFF = [];
    protected $DEA = [];
    protected $MACD = [];

    public function diff($code)
    {
        //1st try
        //选择起始日期, 若不选择, 默认为2010年1月1日  //结束日期默认为当日
        //拿到起始日期至今的收盘价, 开始循环计算EMA12与EMA26
        //得到每一天的DIFF
        //得到每一天的DEA
        //得到MACD
        //成表+作图
        $code = "1".$code;
        $start= 20150227;
        $end=date('Ymt', time());
        $url = "http://quotes.money.163.com/service/chddata.html?code={$code}&start={$start}&end={$end}";
        $this->getDATA($url);
        return [
            'date'=> $this->DATE,
            'diff'=> $this->DIFF,
            'dea'=> $this->DEA,
            'macd'=> $this->MACD,
        ];
    }

    /**
     *  $PRICE 其实就是格式化后的$CONTENT, 不想改了
     */
    private function getDATA($url){
        //fixme 应该第一天的EMA都为0

        $PRICE = [];
        $DATE = [];
        $content = \QL\QueryList::get($url)->encoding('UTF-8','GB2312')->rules([])->query()->getHtml();
        $content = collect(explode("\r\n", $content));
        $content->splice(0,1);
        $content->pop();
        foreach ($content as $k=>$v){
            if($v[3]==0){
                continue;
            }
            $PRICE[$k] = explode(",",$v);
        }
        $PRICE = array_reverse($PRICE);
        dd($PRICE);
        $EMA12_a = 2/13;
        $EMA12_b = 11/13;
        $EMA26_a = 2/27;
        $EMA26_b = 25/27;
        $DEA_a = 8/10;
        $DEA_b = 2/10;

        $LENGTH = count($PRICE);

        $EMA12 = [];  //EMA12第一天的值
        $EMA26 = [];  //EMA26第一天的值
        $DIFF = [];
        $DEA = [];
        $MACD = [];
//        $EMA12[0] = $PRICE[0][3];
        $EMA12[0] = 0;
//        $EMA26[0] = $PRICE[0][3];
        $EMA26[0] = 0;
        $DIFF[0] =  0;
        $DEA[0] = 0;
        $MACD[0] = 0;
        for($j = 0; $j < ($LENGTH - 1); $j++) {
            $DATE[$j] = $PRICE[$j][0];
        }
        for($i = 1; $i < ($LENGTH - 1); $i++){
            $PRICE[$i][3] = intval($PRICE[$i][3]);
            //todo 计算EMA12
            if($PRICE[$i][3] == 0){
                $PRICE[$i][3] = $PRICE[$i-1][3];
            }
            $EMA12[$i] = $EMA12_a * $PRICE[$i][3] + $EMA12_b * $EMA12[$i-1];
            //todo 计算EMA26
            $EMA26[$i] = $EMA26_a * $PRICE[$i][3] + $EMA26_b * $EMA26[$i-1];
            //todo 计算DIFF
            $DIFF[$i] = $EMA12[$i] - $EMA26[$i];
            //todo 计算DEA
            $DEA[$i] = $DEA_a * $DEA[$i-1] + $DEA_b * $DIFF[$i];
            //todo 计算MACD柱, 柱状值系数取2
            $MACD[$i] = 2*($DIFF[$i] - $DEA[$i]);
        }
        $this->DATE = $DATE;
        $this->DIFF = $DIFF;
        $this->DEA = $DEA;
        $this->MACD = $MACD;
    }

    public function chart()
    {
        return view('chart');
    }

    public function test(DayController $dayController)
    {
        $_data = $dayController->getX('600756','20100505','20170929');

        dd($_data);
    }

    public function getcode() {
        return "不要再试, 已经存好了";
        $url = "http://quote.eastmoney.com/stock_list.html";
        $content = QueryList::get($url)->find(".quotebody ul li a")->texts();
        $content->pop();
        $save = [];
        foreach ($content as $k=>$v){
            $v = str_replace(")", "", $v);
//            $content[$k] = explode("(", $v);
            $v = explode("(", $v);

            $save[$k] = [
                'code' => $v[1],
                'name' => $v[0]
            ];
        }

        DB::table('stocks')->insert($save);
        return "存储完毕";

    }


}
