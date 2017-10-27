<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/21
 * Time: 15:38
 */

namespace App\Http\Repositories\MACD;


use App\Http\Repositories\GetXRepository;
use App\Models\stock;
use Illuminate\Support\Facades\Storage;
use QL\QueryList;

class MACDRepo extends GetXRepository
{
    protected $max_key;  //最大值的key 用于获得日数据同时拿到历史最大MACD
    protected $WEEK_ARR = [];  //存储每个周最后一天的日期
    protected $MONTH_ARR = [];  //存储每个月最后一天的日期
    protected $dx;  //存储日金X
    protected $wx;  //存储周金X
    protected $mx;  //存储月金X
    protected $delisted = false; //退市标签   status = 1
    protected $new_m = false; //不满二月的新股标签 status = 2
    protected $new_w = false; //不满二周的新股标签 status = 3
    protected $code;

    protected $d_diff;  //最后一日的diff
    protected $w_diff;  //最后一周的diff包括没走完的月
    protected $m_diff;  //最后一月的diff包括没走完的月
    /**
     * 得到沪深主板及中小板股票列表
     */
    public function getMain()
    {
        $url = "http://quote.eastmoney.com/stock_list.html";
        $content = QueryList::get($url)->find(".quotebody ul li a")->texts();
        $content->pop();
        $save = [];
        foreach ($content as $k=>$v){
            $v = str_replace(")", "", $v);
            $v = explode("(", $v);

            $save[$k] = [
                'code' => $v[1],
                'name' => $v[0]
            ];
        }
        return $save;
    }

    /**
     * 300.csv为通过tushare得到的创业表股票列表
     * @return array 300股票列表
     */
    public function getOthersFromTu()
    {
        if(!file_exists(storage_path('public/300.csv'))){
            throw new \Exception('300文件不存在');
        }
        $content = Storage::get('public/300.csv');
        $content = trim(iconv('gbk', 'utf8', $content));
        $content = json_encode($content, JSON_UNESCAPED_UNICODE);
        $content = explode('\r\n', $content);
        array_forget($content, 0);
        list($key, $val) = array_divide($content);
        $save2  = [];
        foreach ($val as $k=>$v){
            $v = explode(',',$v);
            $save2[] = [
                'code'=>$v[1],
                'name'=>$v[2]
            ];
        }
        return $save2;
    }

    /**
     *  主进入方法
     *  InitUrlAndData
     *  $start, $end  format:"20050505" PHP格式为"Ymd"
     */
    public function init($code, $start, $end)
    {
        $this->initUrl($code, $start, $end)
            ->initData();

        return $this;
    }
    
    /**
     * 日K 计算完整的MACD数据
     * @return $this
     */
    public function calDayMACD()
    {
        //fixme 应该第一天的EMA都为0
        if($this->LENGTH == 0 ){  //没有数据, 说明退市了
            $this->delisted = true;
            return $this;
        }
        //EMA12第一天的值 //EMA26第一天的值
        //PHP可以连续赋值
        $EMA12 = $EMA26 = $DIFF = $DEA = $MACD = [];
        $EMA12[0] = $EMA26[0] = $DIFF[0] = $DEA[0] = $MACD[0] = 0;
        $DATE[0] = $this->DATA[0][0];
        for($i = 1; $i < $this->LENGTH; $i++){
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

        //todo 在本函数被触发时顺便完成找到最大值及当日MACD并存入的工作
        $this->saveMaxAndCurOfMACD();
        //todo get Last_Day_diff
        $this->d_diff = $this->DIFF[$this->LENGTH-2];
        return $this;
    }

    public function getX()
    {
        $day = $this->getDayX()->getFiveGold();
        $week = $this->getWeekX()->checkLastOne('week');
        $month = $this->getMonthX()->checkLastOne('month');

        if($this->delisted){ //退市
            stock::where('code', $this->code)->update([
                'status' => 1
            ]);
            //todo 再返回空数组避免其他表存储；
            $day = [];
        }
        if($this->new_m){  //12月内新股
            stock::where('code', $this->code)->update([
                'status'=> 2
            ]);
            $month = [];
        }

        if($this->new_w){  //12周内新股
            stock::where('code', $this->code)->update([
                'status'=> 3
            ]);
            $week = [];
        }
//        提前释放内存试试
        $diffs = [$this->d_diff, $this->w_diff, $this->m_diff];

        $this->new_m = $this->delisted = $this->neww = false;
        $this->WEEK_ARR = $this->MONTH_ARR = $this->DATA = $this->DATE = $this->DEA = $this->MACD= $this->DIFF = $this->LENGTH = [];
        return [$day, $week, $month, $diffs];
    }
    /**
     * from DayRepo
     * 得到最近所有的日金叉信息， status0 金， status1死
     * @return array
     */
    public function getDayX()
    {
        //因为$this->LENGTH为$DATA总长度, 所以要-1, 但是在循环体里,  有$j+1 , 所以要 - 2
        $x = [];
        for($j = 0; $j < ($this->LENGTH - 2); $j++){
            if($this->DIFF[$j] < $this->DEA[$j] && $this->DIFF[$j+1]>$this->DEA[$j+1]){ //金叉
                $x[] = [
                    'date'=>$this->DATE[$j],
                    'macd' => $this->MACD[$j],
                    'diff' => $this->DIFF[$j],
                    'type' => 0
                ];
            }
            if($this->DIFF[$j] < $this->DEA[$j] && $this->DIFF[$j+1]>$this->DEA[$j+1]){ //死叉
                $x[] = [
                    'date'=>$this->DATE[$j],
                    'macd' => $this->MACD[$j],
                    'diff' => $this->DIFF[$j],
                    'type' => 1
                ];
            }
        }
        $this->dx = $x;
        return $this;
    }

    /**
     * 得到月金X
     * @return $this
     */
    public function getWeekX()
    {
        $this->getWeekArray();
        //todo 重新清洗数据

        $_length = count($this->WEEK_ARR);

        if($_length < 12){  //不满12周的股票 这里直接用12个月代替了, 所以注释
            $this->new_w = true;
            return $this;  //直接return $this阻断即可, 因为此flag, 在getX里$week被直接设为空.
        }

        $DATA = [];
        for($i = 0; $i < $_length; $i++){
            $DATA[] = $this->DATA[$this->WEEK_ARR[$i]];
        }
        $DATA[] = $this->DATA[$this->LENGTH - 1];

        $LENGTH = count($DATA);  //得到周数据的长度

        //EMA12第一天的值 //EMA26第一天的值
        //PHP可以连续赋值
        $EMA12 = $EMA26 = $DIFF = $DEA = $MACD = [];
        $EMA12[0] = $EMA26[0] = $DIFF[0] = $DEA[0] = $MACD[0] = 0;
        $DATE[0] = $DATA[0][0];

        for($i = 1; $i < $LENGTH; $i++){
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
        $week_length = count($EMA12);
        for($j = 0; $j < ($week_length - 1); $j++){
            if($DIFF[$j] < $DEA[$j] && $DIFF[$j+1]>$DEA[$j+1]){
                $x[] = [
                    'date'=> $DATE[$j],
                    'macd' => $MACD[$j],
                    'diff' => $DIFF[$j],
                    'type' => 0
                ];
            }
            if($DIFF[$j] > $DEA[$j] && $DIFF[$j+1] < $DEA[$j+1]){
                $x[] = [
                    'date'=> $DATE[$j],
                    'macd' => $MACD[$j],
                    'diff' => $DIFF[$j],
                    'type' => 1
                ];
            }
        }

        $this->wx = $x;
        $this->w_diff = $DIFF[count($DIFF)-1];
        return $this;
    }

    /**
     * 本方法需提前使用$this->getMonthArr(), 当然，我已经提前写入了
     * 得到每个月的MACD及金叉数据
     */
    public function getMonthX()
    {
        $this->getMonthArr();

        $length = count($this->MONTH_ARR);

        if($length < 12){  //过滤并标记不满12个月的股票
            $this->new_m = true;
            return $this; //直接return $this阻断即可, 因为此flag, 在getX里$month被直接设为空.
        }

        $DATA = [];

        for($i = 0; $i< $length - 1; $i++){
            $j = $this->MONTH_ARR[$i];
            //todo 重新清洗数据
            $DATA[] = $this->DATA[$j];
        }
        //todo 补上当前这个月
        $DATA[] = $this->DATA[$this->LENGTH-1];

        $LENGTH = count($DATA);  //todo 得到月数据长度
        //EMA12第一天的值 //EMA26第一天的值
        //PHP可以连续赋值
        $EMA12 = $EMA26 = $DIFF = $DEA = $MACD = [];
        $EMA12[0] = $EMA26[0] = $DIFF[0] = $DEA[0] = $MACD[0] = 0;
        $DATE[0] = $DATA[0][0];
        for($i = 1; $i < $LENGTH ; $i++){
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
                    'diff' => $DIFF[$j],
                    'type' => 0
                ];
            }
            if($DIFF[$j] > $DEA[$j] && $DIFF[$j+1] < $DEA[$j+1]){
                $x[] = [
                    'date'=> $DATE[$j],
                    'macd' => $MACD[$j],
                    'diff' => $DIFF[$j],
                    'type' => 1
                ];
            }
        }
        $this->mx = $x;
        $this->m_diff = $DIFF[count($DIFF)-1];
        return $this;
    }

    /**
     * 得到最近5个月金叉
     * @return array
     */
    public function getFiveGold()
    {
        return $this->filter($this->dx, 5);
    }

    /**
     *  检查最后一个叉是否为金叉
     *  假如最是金叉， 得到它
     */
    public function checkLastOne($word){
        switch ($word){
            case "week":
                $length = count($this->wx);
                if($length == 0 || $this->wx[$length - 1]['type'] != 0){
                    $this->wx = [];
                }
                return $this->filter($this->wx, 1);
                break;
            case 'month':
                $length = count($this->mx);
                if($length == 0 || $this->mx[$length - 1]['type'] != 0){
                    $this->mx = [];
                }
                return $this->filter($this->mx, 1);
                break;
            default:
                throw new \Exception("没有填写word!");
        }
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
        $month_arr[] = $length-1;
//        sleep(0.1);
        $this->MONTH_ARR = $month_arr;
    }

    /**
     * 得到week_arr 用于记录每个星期最后一天在DATE数组中的下标
     */
    private function getWeekArray()
    {
        $length = $this->LENGTH;  //得到总条数目
        $sum = 0; //星期计数器， 计算本周连续开盘日有几天  改初始值：因为是当日和第二天比， 所以预设为1；  再改初始值： 因为week_arr数组从0开始， 为了方便计数， 还是从0开始。
        $week_arr = [];
        for($i = 0; $i < ($length - 2); $i++){
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
        $this->WEEK_ARR[] = $this->LENGTH -1;
//        sleep(0.1);
        return $this;
    }

    /**
     * 用于calDayMACD的找出最大MACD
     */
    protected function saveMaxAndCurOfMACD(){
        $data = $this->getMaxOfMACD()->pkgMaxData();
        stock::where('code', $data['code'])->update([
            'macd_max'=>$data['macd_max'],
            'macd_cur'=>$this->MACD[($this->LENGTH-2)]
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
}