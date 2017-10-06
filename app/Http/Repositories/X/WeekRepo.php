<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/4
 * Time: 17:49
 */

namespace App\Http\Repositories\x;


use App\Http\Repositories\GetXRepository;
use App\Utils\Params;

class WeekRepo extends GetXRepository
{
    public $WEEK_ARR; //用于存储总周期里每段连续的开盘日的天数
    public $delisted; //是否退市、未上市、新股缺失数据
    public $new;
    protected $x;  //所有叉存放的数组， status0 金， status1死
    /**
     * 得到股票的MACD等信息
     * @param $code  股票代码
     * @param $start 开始日期 format-example:20100101
     * @param $end 结束日期
     */
    public function getDATA($code, $start, $end){
        $this->initUrl($code, $start, $end)
            ->initData();
        $this->delisted = false;
        //fixme 应该第一天的EMA都为0
        $LENGTH = count($this->DATA);
        if($LENGTH ==0 ){
            $this->delisted = true;
            return $this;
        }
        //EMA12第一天的值 //EMA26第一天的值
        //PHP可以连续赋值
        $EMA12 = $EMA26 = $DIFF = $DEA = $MACD = [];
        $EMA12[0] = $EMA26[0] = $DIFF[0] = $DEA[0] = $MACD[0] = 0;
        $DATE[0] = $this->DATA[0][0];

        for($i = 1; $i < ($LENGTH - 1); $i++){
            $DATE[$i] = $this->DATA[$i][0];
//            $this->DATA[$i][3] = intval($this->DATA[$i][3]);
            //todo 计算EMA12
            $EMA12[$i] = $this->EMA12_a * $this->DATA[$i][3] + $this->EMA12_b * $EMA12[$i-1];
            //todo 计算EMA26
            $EMA26[$i] = $this->EMA26_a * $this->DATA[$i][3] + $this->EMA26_b * $EMA26[$i-1];
            //todo 计算DIFF
            $DIFF[$i] = $EMA12[$i] - $EMA26[$i];
            //todo 计算DEA
            $DEA[$i] = $this->DEA_a * $DEA[$i-1] + $this->DEA_b * $DIFF[$i];
            //todo 计算MACD柱, 柱状值系数取2
            $MACD[$i] = 2*($DIFF[$i] - $DEA[$i]);
        }

        $this->DATE = $DATE;
        $this->DIFF = $DIFF;
        $this->DEA = $DEA;
        $this->MACD = $MACD;
        $_length = count($this->DIFF);
        $this->LENGTH =$_length;

        return $this;
    }

    /**
     * 得到week_arr 用于记录每个星期最后一天在DATE数组中的下标
     */
    private function getWeekArray()
    {
        $length = $this->LENGTH;  //得到总条数目
        $sum = 0; //星期计数器， 计算本周连续开盘日有几天  改初始值：因为是当日和第二天比， 所以预设为1；  再改初始值： 因为week_arr数组从0开始， 为了方便计数， 还是从0开始。
        $week_arr = [];
        for($i = 0; $i < ($length - 1); $i++){
            if( $this->DATE[$i+1][6] ==  $this->DATE[$i][6] ){
                //todo  如果月份一样
                if( intval($this->DATE[$i+1][8].$this->DATE[$i+1][9]) - intval($this->DATE[$i][8].$this->DATE[$i][9]) == 1){
                    $sum ++;
                }else{
                    //todo 不连续重置星期计数器
                    $week_arr[] = $sum;
                    $sum = 1;
                }
            }else{ //todo 如果和第二天的月份不一样
                //todo 求出当月的总天数
                $days = date('t', strtotime($this->DATE[$i]));
                if( intval($this->DATE[$i][8].$this->DATE[$i][9]) == intval($days) && intval($this->DATE[$i+1][9]) == 1){
                    //todo 如果当天和总天数一致， 且第二天为01， 则连续
                    $sum++;
                }else{
                    //todo 不连续重置星期计数器
                    $week_arr[] = $sum;
                    $sum = 1;
                }
            }
        }
        foreach ($week_arr as $k =>$v){
            if($k != 0){
                $week_arr[$k] = $week_arr[$k-1] + $v;
            }
        }
        $this->WEEK_ARR = $week_arr;

        return $this;
    }

    /**
     * 本方法需提前使用$this->getWeekArr(), 当然，我已经提前写入了
     * 得到每个周的MACD及金叉数据
     */

    //fixme
    public function getX()
    {
        $this->getWeekArray();
        //todo 重新清洗数据
        $DATA = [];
        $_length = count($this->WEEK_ARR);

        if($_length < 2){  //过滤并标记不满2个周的股票
            $this->new = true;
            return $this;
        }


        for($i = 0; $i < $_length; $i++){
            $DATA[] = $this->DATA[$this->WEEK_ARR[$i]];
        }

        $LENGTH = count($DATA);

        //EMA12第一天的值 //EMA26第一天的值
        //PHP可以连续赋值
        $EMA12 = $EMA26 = $DIFF = $DEA = $MACD = [];
        $EMA12[0] = $EMA26[0] = $DIFF[0] = $DEA[0] = $MACD[0] = 0;
        $DATE[0] = $DATA[0][0];
        for($i = 1; $i < ($LENGTH - 1); $i++){
            $DATE[$i] = $DATA[$i][0];
            //todo 计算EMA12
            $EMA12[$i] = $this->EMA12_a * $DATA[$i][3] + $this->EMA12_b * $EMA12[$i-1];
            //todo 计算EMA26
            $EMA26[$i] = $this->EMA26_a * $DATA[$i][3] + $this->EMA26_b * $EMA26[$i-1];
            //todo 计算DIFF
            $DIFF[$i] = $EMA12[$i] - $EMA26[$i];
            //todo 计算DEA
            $DEA[$i] = $this->DEA_a * $DEA[$i-1] + $this->DEA_b * $DIFF[$i];
            //todo 计算MACD柱, 柱状值系数取2
            $MACD[$i] = 2*($DIFF[$i] - $DEA[$i]);
        }
        /**
         * 得到最近所有的叉信息， status0 金， status1死
         * 注意： MA13与MA26， 东方财富通为MA5与MA10， 因此东方财富通会出现小金X，但我的不会， 我们只会在明显金X处出现吻合
         * @return array
         */
        $x = [];
        $month_length = count($EMA12);
        for($j = 0; $j < ($month_length - 1); $j++){
            if($DIFF[$j] < $DEA[$j] && $DIFF[$j+1]>$DEA[$j+1]){
                $x[] = [
                    'date'=> $DATE[$j],
                    'macd' => $MACD[$j],
                    'type' => 0
                ];
            }
            if($DIFF[$j] > $DEA[$j] && $DIFF[$j+1] < $DEA[$j+1]){
                $x[] = [
                    'date'=> $DATE[$j],
                    'macd' => $MACD[$j],
                    'type' => 1
                ];
            }
        }
        $this->x = $x;
        return $this;
    }

    /**
     * 假如最后1个叉是金叉， 得到它
     */
    public function getLastGold()
    {
        $this->checkLastOne();
        $this->filter($this->x, 1);
        return $this->filterArr;
    }

    /**
     *  检查最后一个叉是否为金叉
     */
    public function checkLastOne(){
        $length = count($this->x);
        //有些新上的股连周金X都没有。
        if($length == 0 || $this->x[$length - 1]['type'] != 0){
            $this->x = [];
        }
        return $this;
    }
}