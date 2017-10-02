<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use QL\QueryList;

class SohuController extends Controller
{
    protected $DATE;
    protected $DIFF;
    protected $DEA;
    protected $MACD;
    protected $status;

    public function index()
    {
        return view('chart_sohu');
    }

    public function getx($code)
    {
        $url = "http://q.stock.sohu.com/hisHq?code=cn_{$code}&start=20150205&end=20170930&stat=1&order=D&period=d&callback=historySearchHandler&rt=jsonp";

        $content = QueryList::get($url)->encoding('UTF-8','GB2312')->rules([])->query()->getHtml();
        $content = str_replace("historySearchHandler([", "", $content);
        $content = substr($content, 0, strlen($content)-3);

        $content = json_decode($content, JSON_UNESCAPED_UNICODE);
        $content = array_reverse($content['hq']);

        $this->getDATA($content);
        $length = count($this->DATE);

        $x = [];
        for($i = 1; $i < ($length -1); $i++){
        //todo 计算金叉
            if($this->DIFF[$i] > $this->DEA[$i] && $this->DIFF[$i-1] < $this->DEA[$i-1] ){
                $x[] = [
                    "type" => "金X",
                    "date" => $this->DATE[$i-1],
                    "status" => $this->status
                ];
            }
        }
        return $x[count($x)-1];
    }

    private function getDATA($content){
        //fixme 应该第一天的EMA都为0
        $DATE = [];

        $EMA12_a = 2/13;
        $EMA12_b = 11/13;
        $EMA26_a = 2/27;
        $EMA26_b = 25/27;
        $DEA_a = 8/10;
        $DEA_b = 2/10;

        $LENGTH = count($content);

        $EMA12 = [];  //EMA12第一天的值
        $EMA26 = [];  //EMA26第一天的值
        $DIFF = [];
        $DEA = [];
        $MACD = [];
        $EMA12[0] = 0;
        $EMA26[0] = 0;
        $DIFF[0] =  0;
        $DEA[0] = 0;
        $MACD[0] = 0;
        $DATE[0] = $content[0][0];

        for($i = 1; $i < $LENGTH; $i++) {
            $DATE[$i] = $content[$i][0];
//            $content[$i][3] = intval($content[$i][3]);
            //todo 计算EMA12
            $EMA12[$i] = $EMA12_a * $content[$i][2] + $EMA12_b * $EMA12[$i-1];
            //todo 计算EMA26
            $EMA26[$i] = $EMA26_a * $content[$i][2] + $EMA26_b * $EMA26[$i-1];
            //todo 计算DIFF
            $DIFF[$i] = $EMA12[$i] - $EMA26[$i];
            //todo 计算DEA
            $DEA[$i] = $DEA_a * $DEA[$i-1] + $DEA_b * $DIFF[$i];
            //todo 计算MACD柱, 柱状值系数取2
            $MACD[$i] = 2*($DIFF[$i] - $DEA[$i]);
        }

        //todo 检查数据完整性
        if($this->checkData($DATE) == 0){
           dd("数据不齐");
        }

        $this->DATE = $DATE;
        $this->DIFF = $DIFF;
        $this->DEA = $DEA;
        $this->MACD = $MACD;
    }

    //todo 数据不齐报警模块
    private function checkData($date){
        $length = count($date);
        //todo 求得这段时间的总天数
        $days1 = strtotime($date[0]);
        $days2 = strtotime($date[$length - 1]);
        $day = ceil(($days2-$days1)/1000/86400);
        //todo 求得这段时间的非开盘日期
        $years = ceil($day/365);
        $remove = $years * 115;

    //那么数据差不能超过(week * 2.4), 一年法定假日大约11天, 一年总共休息日为115天

//    console.log(day, date.length, `差值:${day - date.length - remove}`, `非开盘日:${remove}`)
        if($day - $length  > $remove){
            $this->status = 0; //"数据不齐"
        }else {
            $this->status = 1; //"数据较完备"
        }

        return $this->status;
    }
}
