<?php

function selectMax($arr){
    $max = $arr[0];
    $length = count($arr);
    for($i = 0 ; $i< ($length - 1); $i++){
        if($arr[$i] < $arr[$i+1]){
            $max = $arr[$i+1];
        }
    }
    return $max;
}

/**
 * 为了让取缓存的代码变得短一点
 */
function getC($key){
    return \Illuminate\Support\Facades\Cache::get($key);
}

/**
 * @author injection(injection.mail@gmail.com)
 * @var date1日期1
 * @var date2 日期2
 * @var tags 年月日之间的分隔符标记,默认为'-'
 * @return 相差的月份数量
 * @example:
$date1 = "2003-08-11";
$date2 = "2008-11-06";
$monthNum = getMonthNum( $date1 , $date2 );
echo $monthNum;
 */
function getMonthNum( $date1, $date2, $tags='-' ){
    $date1 = explode($tags,$date1);
    $date2 = explode($tags,$date2);
    return abs($date1[0] - $date2[0]) * 12 + abs($date1[1] - $date2[1]);
}