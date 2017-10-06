<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/4
 * Time: 17:49
 */

namespace App\Http\Repositories\x;


use App\Http\Repositories\GetXRepository;

class MonthRepo extends GetXRepository
{
    public $MONTH_ARR;
    public $delisted; //是否退市、未上市、新股缺失数据
    public $new; //新股等，有数据但数据没有满一个月
    protected $x;
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
     * @return array $month_arr 每个月最后一个开盘日的数据
     */
    private function getMonthArr(){
        $length = count($this->DIFF);
        $month_arr = [];
        //todo 找到当月最后一天 & 得到那一天的下标
        for($i = 0; $i < ($length-1); $i++){
            if (intval($this->DATE[$i][6]) !=intval($this->DATE[$i+1][6])){
                //如果相邻两个开盘日的月份数不同，那么当天一定是当月最后一个开盘日
                //todo 本if记前不记后！避免X月刚开始时记成X月第一个开盘日!
                $month_arr[] = $i;
            }
        }
        //todo 最后一个月没有记上， 补上他的最后一日
        $month_arr[] = $length;
        $this->MONTH_ARR = $month_arr;
    }

    /**
     * 本方法需提前使用$this->getMonthArr(), 当然，我已经提前写入了
     * 得到每个月的MACD及金叉数据
     */

    public function getX()
    {
        $this->getMonthArr();
        $x = [];
        $this->new = false;

        $length = count($this->MONTH_ARR);
        if($length < 2){  //过滤并标记不满2个月的股票
            $this->new = true;
            return $this;
        }

        $DATA = [];


        for($i = 0; $i< $length; $i++){
            $j = $this->MONTH_ARR[$i];
            //todo 重新清洗数据
            $DATA[] = $this->DATA[$j];
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
         * 得到最近的月金叉 + 死叉  type0 金， type1 死
         * 注意： MA13与MA26， 东方财富通为MA5与MA10， 因此东方财富通会出现小金X，但我的不会， 我们只会在明显金X处出现吻合
         * @return array
         */
        $x = [];
        $month_length = count($EMA12);

        for($j = 0; $j < ($month_length - 1); $j++){
            //todo 优化， 是让X的date更接近启动日， 还是提前于启动日？
//             $dif1 = $DIFF[$j];
//             $dif2 = $DIFF[$j+1];
//             $dea1 = $DEA[$j];
//             $dea2 = $DEA[$j+1];

            if($DIFF[$j] < $DEA[$j] && $DIFF[$j+1]>$DEA[$j+1]){
                //todo 优化， 是让X的date更接近启动日， 还是提前于启动日？
                $x[] = [
                    'date'=> $DATE[$j+1],    //若不+1， 对比600009 4.28金叉，我算出在3.31？
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
        $this->filter($this->x, 1);
        return $this->filterArr;
    }

    /**
     *  检查最后一个叉是否为金叉
     */
    public function checkLastOne(){
        $length = count($this->x);
        //有些新上的股连周金X都没有。 or 上一个叉是死叉
        if($length == 0 || $this->x[$length - 1]['type'] != 0){
            $this->x = [];
        }
        return $this;
    }
}