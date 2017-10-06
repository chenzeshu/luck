<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/4
 * Time: 15:34
 */

namespace App\Http\Repositories;

use App\Utils\Params;

class GetXRepository
{
    public $DATA;
    public $DATE;
    public $DIFF;
    public $DEA;
    public $MACD;
    public $LENGTH;  //日期数组长度（其实也是上述）
    public $url;  //组装的网易API

    public $EMA12_a;
    public $EMA12_b;
    public $EMA26_a;
    public $EMA26_b;
    public $DEA_a;
    public $DEA_b;

    protected $filterArr;

    function __construct()
    {
        //todo 得到 data并初始化各个值

    }

    /**
     * 得到最近10个日金叉
     * @return array
     */
    public function getDay()
    {
        $x = [];
        for($j = 0; $j < ($this->LENGTH-1); $j++){
            if($this->DIFF[$j] < $this->DEA[$j] && $this->DIFF[$j+1]>$this->DEA[$j+1]){
                $x[] = [
                    'date'=>$this->DATE[$j],
                    'macd' => $this->MACD[$j]
                ];
            }
        }
        return $x;
    }

    /**
     * 1.判断最近的1个叉是否是金叉，如果不是， 返回null, 如果是， 返回数据
     */
    public function getWeek()
    {
        //todo 1： 将日期换算成星期，然后取星期最后一天（考虑节假日）
    }

    /**
     * 1.判断最近的1个叉是否是金叉，如果不是， 返回null, 如果是， 返回数据
     */
    public function getMonth()
    {
        
    }
    
    public function initUrl($code, $start, $end)
    {
        //todo
        //1:计算最后10个月X
        //2:如果最后一个X是金x,保存，
        //3:每天一次？其实每天一次与每月一次没有什么区别
        //4:先采用网易api剔除停盘数据

        if($code[0] == 6){
            $code = "0". $code;
        }else {
            $code = "1". $code;
        }
        //todo 先用600756做一个测试
        //todo 后期考虑使用env() + printf
          $this->url = "http://quotes.money.163.com/service/chddata.html?code={$code}&start={$start}&end={$end}";

        return $this;
    }

    public function initData()
    {
        $DATA = [];
        $content = \QL\QueryList::get($this->url)->encoding('UTF-8','GB2312')->rules([])->query()->getHtml();
        $content = collect(explode("\r\n", $content));
        $content->splice(0,1);
        $content->pop();

        foreach ($content as $k=>$v){
            $v = explode(",",$v);
            if($v[3]==0){
                continue;
            }
            $DATA[] = $v;
        }

        $this->DATA = array_reverse($DATA);
        $this->EMA12_a = Params::EMA12_a;
        $this->EMA12_b = Params::EMA12_b;
        $this->EMA26_a = Params::EMA26_a;
        $this->EMA26_b = Params::EMA26_b;
        $this->DEA_a = Params::DEA_a;
        $this->DEA_b = Params::DEA_b;

        return $this;
    }

    /**
     * 截取数组函数
     * @param $x
     * @param $num
     * @param $type 0:金叉， 1：死叉
     * @return array
     */
    protected function filter($x, $num, $type = 0){
        //避免传入空数组（有些新股没有月，周金X）
        if(count($x) == 0){
            $this->filterArr = [];
            return $this;
        }

        $y = [];
        foreach ($x as $k=>$v){
            if($v['type'] == $type){
              $y[] = $v;
            }
        }
        $length = count($y);
        $this->filterArr = array_slice($y, ($length - $num));

        return $this;
    }



}