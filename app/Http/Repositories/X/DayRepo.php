<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/4
 * Time: 17:49
 */

namespace App\Http\Repositories\x;

use App\Http\Repositories\GetXRepository;
use App\Models\stock;

class DayRepo extends GetXRepository
{
    protected $x; //全部叉的数组
    protected $max_key;  //macd最大的key
    public $delisted; //是否退市、未上市、新股缺失数据
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

        //todo 在本函数被触发时顺便完成找到最大值并存入的工作
        $this->saveMaxOfMACD();

        return $this;
    }

    protected function saveMaxOfMACD(){
       $data = $this->getMaxOfMACD()->pkgMaxData();
       stock::where('code', $data['code'])->update([
           'macd_max'=>$data['macd_max']
       ]);
    }

    /**
     * 得到macd最大值的key, 然后从DATA中找出对应那一列
     */
    protected function getMaxOfMACD(){
        $max_key = 0;
        $len = count($this->MACD);
        for($i = 0; $i < ($len-1); $i++){
            if($this->MACD[$max_key] < $this->MACD[$i+1]){
                $max_key = $i+1;
            }
        }
        $this->max_key = $max_key;
        return $this;
    }

    /**
     * 打包最大值数据为易于存储的格式
     */
    protected function pkgMaxData(){
        return [
            'code' => substr($this->DATA[$this->max_key][1],1,6),
            'macd_max' => $this->MACD[$this->max_key]
        ];
    }

    /**
     * 得到最近所有的叉信息， status0 金， status1死
     * @return array
     */
    public function getX()
    {
        $x = [];
        for($j = 0; $j < ($this->LENGTH-1); $j++){
            if($this->DIFF[$j] < $this->DEA[$j] && $this->DIFF[$j+1]>$this->DEA[$j+1]){ //金叉
                $x[] = [
                    'date'=>$this->DATE[$j],
                    'macd' => $this->MACD[$j],
                    'type' => 0
                ];
            }
            if($this->DIFF[$j] < $this->DEA[$j] && $this->DIFF[$j+1]>$this->DEA[$j+1]){ //死叉
                $x[] = [
                    'date'=>$this->DATE[$j],
                    'macd' => $this->MACD[$j],
                    'type' => 1
                ];
            }
        }
        $this->x = $x;
        return $this;
    }

    /**
     * 得到最近5个金叉
     * @return array
     */
    public function getFiveGold()
    {
        $this->filter($this->x, 5);
        return $this->filterArr;
    }

}